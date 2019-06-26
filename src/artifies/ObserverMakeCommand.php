<?php

namespace Artify\Artify\Artifies;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Console\ObserverMakeCommand as BasicObserverMakeCommand;

class ObserverMakeCommand extends BasicObserverMakeCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

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
    public function __construct(Filesystem $files)
    {
        parent::__construct($files);
        $this->setName('artify:observer');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');

        if ($this->option('model')) {
            $this->call('make:model', ['name' => $this->option('model')]);
        }

        parent::handle();
        if (!str_contains($name, '\\')) {
            $name = '\\App\\Observers\\' . $name;
        }

        if ($this->files->exists(app_path('/Providers/EloquentEventServiceProvider.php')) && $this->option('model')) {
            $provider = $this->files->get(app_path('/Providers/EloquentEventServiceProvider.php'));
            $replacement = str_replace_last(
                'class);',
                "class);\n\t\t{$this->option('model')}::observe($name::class);",
                $provider
            );

            $this->files->put(app_path('/Providers/EloquentEventServiceProvider.php'), $replacement);
        }
        if ($this->option('model') && !$this->files->exists(app_path('/Providers/EloquentEventServiceProvider.php'))) {
            $this->call('make:provider', ['name' => 'EloquentEventServiceProvider']);

            $provider = $this->files->get(app_path('/Providers/EloquentEventServiceProvider.php'));
            $replacement = str_replace('public function boot()
    {
        //', "public function boot()\n\t{\n\t\t{$this->option('model')}::observe($name::class);", $provider);
            $this->files->put(app_path('/Providers/EloquentEventServiceProvider.php'), $replacement);

            $this->registerProvider();
        }
    }

    public function registerProvider()
    {
        $config = $this->files->get(config_path('/app.php'));

        if (!strpos($config, "App\Providers\EloquentEventServiceProvider::class")) {
            $providerRegistration = str_replace(
                "App\Providers\AppServiceProvider::class,",
                "App\Providers\AppServiceProvider::class,\n\t\tApp\Providers\EloquentEventServiceProvider::class,",
                $config
            );

            $this->files->put(config_path('/app.php'), $providerRegistration);
        }
    }
}
