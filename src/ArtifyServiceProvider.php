<?php

namespace Artify\Artify;

use Illuminate\Support\ServiceProvider;

class ArtifyServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->registerConsoleCommands();
        }
        $this->registerPublishables();
        $this->registerHelpers();
    }

    public function registerHelpers()
    {
        require_once __DIR__.'/Helpers/Helpers.php';
    }

    public function registerConsoleCommands()
    {
        $this->commands(\Artify\Artify\Artifies\UserAssignCommand::class); //
        $this->commands(\Artify\Artify\Artifies\RegisterAuthorizationCommand::class); //
        $this->commands(\Artify\Artify\Artifies\FacadeMakeCommand::class); //
        $this->commands(\Artify\Artify\Artifies\ResponseMakeCommand::class); //
        $this->commands(\Artify\Artify\Artifies\ObserverMakeCommand::class); //
        $this->commands(\Artify\Artify\Artifies\GenerateCrudCommand::class);
        $this->commands(\Artify\Artify\Artifies\RepositoryMakeCommand::class); //
        $this->commands(\Artify\Artify\Artifies\InstallCommand::class); //
    }

    public function registerPublishables()
    {
        $publishablePath = __DIR__.'/publishables';
        $this->publishes([
            $publishablePath.'/config/artify.php' => config_path('artify.php'),
            $publishablePath.'/migrations'        => database_path('migrations'),
            $publishablePath.'/seeders'           => database_path('seeds'),
        ], 'artify');
    }
}
