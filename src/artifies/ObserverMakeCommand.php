<?php

namespace Artify\Artify\Artifies;

use File;
use Illuminate\Console\Command;

class ObserverMakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'artify:observer
                            {name : The name of the observer to be created}
                            {--m|model : Creates the model for this observer}
                            {--p|provider=true : Creates the events eloquent service provider for observers}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates an observer for your models';

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
        $model = strpos($name, 'Observer') ? explode('Observer', $name)[0] : $name;

        if (!is_dir(app_path('/Observers'))) {
            File::makeDirectory(app_path('/Observers'));
        }

        if (File::exists(app_path('/Observers/'.$name.'.php'))) {
            return $this->error('Observer already exists');
        }

        if (File::exists(app_path('/Providers/EloquentEventServiceProvider.php'))) {
            $provider = File::get(app_path('/Providers/EloquentEventServiceProvider.php'));
            $replacement = str_replace_last(
                'class);',
                "class);\n\t\t\App\\$model::observe(\App\Observers\\$name::class);",
                $provider
            );

            File::put(app_path('/Providers/EloquentEventServiceProvider.php'), $replacement);
        }

        if ($this->option('model')) {
            $this->call('make:model', ['name' => $model]);
        }

        if ($this->option('provider')) {
            $this->call('make:provider', ['name' => 'EloquentEventServiceProvider']);

            $provider = File::get(app_path('/Providers/EloquentEventServiceProvider.php'));
            $replacement = str_replace('public function boot()
    {
        //', "public function boot()\n\t{\n\t\t\App\\$model::observe(\App\Observers\\$name::class);", $provider);
            File::put(app_path('/Providers/EloquentEventServiceProvider.php'), $replacement);

            $this->registerProvider();
        }

        $defaultObserverContent = File::get(artify_path('artifies/stubs/DummyObserver.stub'));
        $runtimeObserverContent = str_replace('Dummy', $model, $defaultObserverContent);
        File::put(artify_path('artifies/stubs/DummyObserver.stub'), $runtimeObserverContent);
        File::copy(artify_path('artifies/stubs/DummyObserver.stub'), app_path('/Observers/'.$name.'.php'));
        File::put(artify_path('artifies/stubs/DummyObserver.stub'), $defaultObserverContent);

        $this->info('Yateey! observer created successfully');
    }

    public function registerProvider()
    {
        $config = File::get(config_path('/app.php'));
        $providerRegistration = str_replace(
            "App\Providers\AppServiceProvider::class,",
            "App\Providers\AppServiceProvider::class,\n\t\tApp\Providers\EloquentEventServiceProvider::class,",
            $config
        );

        File::put(config_path('/app.php'), $providerRegistration);
    }
}
