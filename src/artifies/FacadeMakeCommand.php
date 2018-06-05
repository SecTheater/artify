<?php

namespace Artify\Artify\Artifies;

use File;
use Illuminate\Console\Command;

class FacadeMakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'artify:facade {name : The name of the facade to be created}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a facade for your application';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');
        if (strpos($name, 'Repository')) {
            $model = explode('Repository', $name)[0];
            if (file_exists(app_path('/Providers/EloquentServiceProvider.php'))) {
                $provider = File::get(app_path('/Providers/EloquentServiceProvider.php'));
                $replacement = str_replace(
                    ["use App\{", "use App\Repositories\{", "public function register()
    {"],
                    ["use App\{$model, ", "use App\Repositories\{$name, ", "public function register()
    {\n\t\t" . '$this' . "->app->singleton('$name', function () {\n\t\t\treturn new $name(new $model);\n\t\t});\n"],
                    $provider
                );
                File::put(app_path('/Providers/EloquentServiceProvider.php'), $replacement);
            }

            if (!File::exists(app_path('/Providers/EloquentServiceProvider.php'))) {
                if ($this->confirm("The service provider for eloquent repositories does not exist! Do you want to create it?")) {
                    $defaultProviderContent = File::get(artify_path('artifies/stubs/EloquentServiceProvider.stub'));
                    $runtimeProviderContent = str_replace('Dummy', $model, $defaultProviderContent);
                    File::put(artify_path('artifies/stubs/EloquentServiceProvider.stub'), $runtimeProviderContent);
                    File::copy(artify_path('artifies/stubs/EloquentServiceProvider.stub'), app_path('/Providers/EloquentServiceProvider.php'));
                    File::put(artify_path('artifies/stubs/EloquentServiceProvider.stub'), $defaultProviderContent);

                    $config = File::get(config_path('/app.php'));

                    $providerRegistiration = str_replace(
                        "App\Providers\AppServiceProvider::class,",
                        "App\Providers\AppServiceProvider::class,\n\t\tApp\Providers\EloquentServiceProvider::class,",
                        $config
                    );

                    File::put(config_path('/app.php'), $providerRegistiration);
                }
            }
        } elseif (strpos($name, 'Facade')) {
            $model = explode('Facade', $name)[0];
        } else {
            $model = $name;
        }

        if (!is_dir(app_path('/Facades')))
            File::makeDirectory(app_path('/Facades'));

        if (file_exists(app_path('/Facades/' . $name . '.php')))
            return $this->error('Facade already exists');

        $defaultFacadeContent = File::get(artify_path('artifies/stubs/DummyFacade.stub'));
        $runtimeFacadeContent = str_replace('DummyFacade', $name, $defaultFacadeContent);
        File::put(artify_path('artifies/stubs/DummyFacade.stub'), $runtimeFacadeContent);
        File::copy(artify_path('artifies/stubs/DummyFacade.stub'), app_path('/Facades/' . $name . '.php'));
        File::put(artify_path('artifies/stubs/DummyFacade.stub'), $defaultFacadeContent);

        $config = File::get(config_path('/app.php'));

        if (!strpos($config, "App\Facades\\" . $name . "::class")) {
            $aliasRegistration = str_replace(
                "'aliases' => [\n",
                "'aliases' => [\n\t\t'$name' => App\Facades\\$name::class,\n",
                $config
            );

            File::put(config_path('/app.php'), $aliasRegistration);
        }

        $this->info("Facade created successfully! Congrats");
    }
}
