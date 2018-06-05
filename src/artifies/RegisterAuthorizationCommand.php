<?php

namespace Artify\Artify\Artifies;

use Illuminate\Console\Command;
use Illuminate\Console\DetectsApplicationNamespace;
use Illuminate\Filesystem\Filesystem;

class RegisterAuthorizationCommand extends Command
{
    use DetectsApplicationNamespace;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'artify:register-authorization {--i|import-roles}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setting Artify Authorization Policy & Gates.';
    private $filesystem;
    protected $permissions = [];
    protected $views = [
        'artifies/stubs/AuthyServiceProvider.stub' => 'Providers/AuthyServiceProvider.php',
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();
        $this->filesystem = $filesystem;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Gathering Information of Roles');
        $roleModel = config('artify.models.namespace') . config('artify.models.role');
        $permissions = (new $roleModel)->pluck(config('artify.permissions_column'))->collapse();
        $userModel = config('artify.models.namespace') . config('artify.models.user');
        if (!count($permissions)) {
            return $this->error('Roles are not set yet, or maybe they are not an array.');
        }
        if(!method_exists(new $userModel, 'hasRole')){
            return $this->error('You should import the Roles Trait, or create your own hasRole method, As gates need this method.');
        }
        foreach ($permissions as $key => $value) {
            $key = explode('-', $key);
            $this->permissions[str_plural($key[1])][] = $key[0];
        }
        $this->exportViews();
    }

    protected function exportViews()
    {
        $this->info('Registering Policies & Gates !');
        foreach ($this->views as $key => $view) {
            $originalContent = $this->filesystem->get(artify_path($key));
            $content = $this->appendNamespaces($originalContent);
            $content = $this->appendPolicies($content, $this->permissions);
            $content = $this->appendGates($content, array_keys($this->permissions));
            foreach ($this->permissions as $permission => $value) {
                $model = str_singular(ucfirst($permission));
                $content = $this->replaceNamespaces($model, $content);
                $content = $this->replacePolicies($model, $content);
                $content = $this->replaceArraySegements($model, $content);
                $content = $this->replaceGates($model, $content, $value);
                $this->generatePolicyStub($model, $value);
            }
            $content = str_replace(['use App\DummyModel;','use App\Policies\DummyPolicy;'], ['',''], $content);
            $this->filesystem->put(artify_path($key), $content);
            copy(
                artify_path($key),
                app_path($view)
            );
            $this->filesystem->put(artify_path($key), $originalContent);
        }
        $content = $this->filesystem->get(config_path('app.php'));
        if (!strpos($content, 'App\Providers\AuthyServiceProvider::class')) {
            $this->info('Registering Authy Service Provider ');
            $content = str_replace('App\Providers\AuthServiceProvider::class,', "App\Providers\AuthServiceProvider::class,\n\t\t\t\tApp\Providers\AuthyServiceProvider::class,", $content);
            $this->filesystem->put(config_path('app.php'), $content);
        } else {
            $this->info('It Seems that Authy Service Provider has been registered earlier.');
        }
    }

    protected function appendNamespaces($content)
    {
        return str_replace("namespace App\Providers;", "namespace App\Providers;\n".str_repeat("use App\DummyModel;\nuse App\Policies\DummyPolicy;", count($this->permissions)), $content);
    }

    protected function appendPolicies($content, $policies)
    {
        return str_replace('App\DummyModel::class => App\Policies\DummyPolicy::class', str_repeat("App\DummyModel::class => App\Policies\DummyPolicy::class\t\t", count($policies)), $content);
    }

    protected function appendGates($content, $policies)
    {
        $roleModel = config('artify.models.namespace') . config('artify.models.role');
        $permissions = (new $roleModel)->pluck(config('artify.permissions_column'))->collapse();

        return str_replace('Gate::define(\'dummy-access\',\'\App\Policies\DummyPolicy@DummyAction\');', str_repeat("Gate::define(\'dummy-access\',\'\App\Policies\DummyPolicy@DummyAction\');\n\t\t", $permissions->count()), $content);
    }

    protected function replaceNamespaces($model, $content)
    {
        return str_replace_first("use App\DummyModel;", 'use ' . config('artify.models.namespace'). ucfirst($model) . ';', $content);
    }

    protected function replacePolicies($model, $content)
    {
        return str_replace_first("use App\Policies\DummyPolicy;", "use App\Policies\\{$model}Policy;\n", $content);
    }

    protected function replaceArraySegements($model, $content)
    {
        return  str_replace_first("App\DummyModel::class => App\Policies\DummyPolicy::class", ucfirst($model) . "::class => App\Policies\\{$model}Policy::class,\n", $content);
    }

    protected function replaceGates($model, $content, $permissions)
    {
        foreach ($permissions as $permission) {
            $access = $permission.'-'.lcfirst($model);
            $content = str_replace_first("Gate::define(\'dummy-access\',\'\App\Policies\DummyPolicy@DummyAction\');\n", "Gate::define('$access','{$model}Policy@$permission');\n", $content);
        }

        return $content;
    }

    protected function generatePolicyStub($model, $permissions)
    {
        $originalContent = $content = $this->filesystem->get(artify_path('artifies/stubs/Policy.stub'));
        if (str_contains($model, 'User')) {
            str_replace('use NamespacedDummyModel;', '', $originalContent);
        } else {
            $content = str_replace('use NamespacedDummyModel;', 'use ' . config('artify.models.namespace') . ucfirst($model) . ';', $originalContent);
        }
        if (in_array('approve', $permissions)) {
            $content = str_replace('use HandlesAuthorization;', "use HandlesAuthorization;\n\tpublic function approve(User \$user,".ucfirst($model).' $'.lcfirst($model).")\n\t{\n\t\treturn \$user->hasRole('approve-".lcfirst($model)."') || \$user->id == \$".lcfirst($model)."->user_id;\n\t}", $content);
        }
        $content = str_replace([
            'DummyClass',
            'DummyModel',
            'dummyModel',
            '-dummy',
            '$dummy'
        ], [
            "{$model}Policy",
            $model,
            lcfirst($model),
            '-'.lcfirst($model),
            '$'.lcfirst($model)
        ], $content);
        if (!\File::exists(app_path('Policies/'."{$model}Policy.php"))) {
            \File::makeDirectory(app_path('Policies'), 0755, false, true);
        }
        $this->filesystem->put(artify_path('artifies/stubs/Policy.stub'), $content);
        copy(
                artify_path('artifies/stubs/Policy.stub'),
                app_path('Policies/'."{$model}Policy.php")
        );
        $this->filesystem->put(artify_path('artifies/stubs/Policy.stub'), $originalContent);
    }
}
