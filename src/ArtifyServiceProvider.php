<?php

namespace Artify\Artify;

use Artify\Artify\Artifies\Tenant\DatabaseFreshCommand;
use Artify\Artify\Artifies\Tenant\DatabaseMigrationCommand;
use Artify\Artify\Artifies\Tenant\DatabaseRefreshCommand;
use Artify\Artify\Artifies\Tenant\DatabaseResetCommand;
use Artify\Artify\Artifies\Tenant\DatabaseRollbackCommand;
use Artify\Artify\Artifies\Tenant\DatabaseSeedCommand;
use Artify\Artify\Contracts\Models\Tenant;
use Artify\Artify\Tenant\Database\DatabaseAdapter;
use Artify\Artify\Tenant\Database\DatabaseFactory;
use Artify\Artify\Tenant\Database\DatabaseManager;
use Artify\Artify\Tenant\Manager;
use Illuminate\Database\DatabaseManager as BaseDatabaseManager;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;

class ArtifyServiceProvider extends ServiceProvider {
	/**
	 * Bootstrap services.
	 *
	 * @return void
	 */
	public function boot() {
		Filesystem::macro('getFileName', function ($name) {
			return array_last(explode('\\', $name));
		});
		Filesystem::macro('getNamespaceFromLocation', function ($location) {
			return ucfirst(str_replace('/', '\\', $location));
		});
		Filesystem::macro('transformNamespaceToLocation', function ($location) {
            $location = str_replace('\\', '/', $location);
            $filename = array_last(explode('/', $location));
            return str_replace('/' . $filename, '', $location);
		});
		$this->app->singleton(Manager::class, function () {
			return new Manager();
		});
		$this->app->when(DatabaseAdapter::class)->needs('$connection')->give(config('database.default'));
		$this->app->singleton(DatabaseFactory::class, function () {
			return new DatabaseFactory(app(DatabaseAdapter::class));
		});
		$this->app->singleton(DatabaseManager::class, function () {
			return new DatabaseManager(app(BaseDatabaseManager::class), app(DatabaseFactory::class));
		});

	}

	/**
	 * Register services.
	 *
	 * @return void
	 */
	public function register() {
		if ($this->app->runningInConsole()) {
			$this->registerConsoleCommands();
			$this->app->singleton(DatabaseMigrationCommand::class, function ($app) {
				return new DatabaseMigrationCommand($app->make('migrator'), $app->make(DatabaseManager::class));
			});
			$this->app->singleton(DatabaseRollbackCommand::class, function ($app) {
				return new DatabaseRollbackCommand($app->make('migrator'), $app->make(DatabaseManager::class));
			});
			$this->app->singleton(DatabaseResetCommand::class, function ($app) {
				return new DatabaseResetCommand($app->make('migrator'), $app->make(DatabaseManager::class));
			});
			$this->app->singleton(DatabaseRefreshCommand::class, function ($app) {
				return new DatabaseRefreshCommand($app->make('migrator'), $app->make(DatabaseManager::class));
			});
			$this->app->singleton(DatabaseFreshCommand::class, function ($app) {
				return new DatabaseFreshCommand($app->make(DatabaseManager::class));
			});
			$this->app->singleton(DatabaseSeedCommand::class, function ($app) {
				return new DatabaseSeedCommand($app->make('db'), $app->make(DatabaseManager::class));
			});

		}
		$this->registerPublishables();
		$this->registerHelpers();
	}

	public function registerHelpers() {
		require_once __DIR__ . '/Helpers/Helpers.php';
	}

	public function registerConsoleCommands() {

		$this->commands(\Artify\Artify\Artifies\RegisterAuthorizationCommand::class); // needs refactoring.
		$this->commands(\Artify\Artify\Artifies\GenerateCrudCommand::class); // needs refactoring.
		$this->commands(\Artify\Artify\Artifies\RepositoryMakeCommand::class); // needs refactoring.
		$this->commands(\Artify\Artify\Artifies\InstallCommand::class); //
		$this->commands(\Artify\Artify\Artifies\ADRInstallCommand::class); //
		$this->commands(\Artify\Artify\Artifies\ADRCommandGenerator::class); // needs refactoring.

		$this->commands(\Artify\Artify\Artifies\Tenant\DatabaseMigrationCommand::class); //
		$this->commands(\Artify\Artify\Artifies\Tenant\DatabaseRollbackCommand::class); //
		$this->commands(\Artify\Artify\Artifies\Tenant\DatabaseSeedCommand::class); //
		$this->commands(\Artify\Artify\Artifies\Tenant\DatabaseFreshCommand::class); //
		$this->commands(\Artify\Artify\Artifies\Tenant\DatabaseResetCommand::class); //
		$this->commands(\Artify\Artify\Artifies\Tenant\DatabaseRefreshCommand::class); //

		$this->commands(\Artify\Artify\Artifies\ObserverMakeCommand::class); //
		$this->commands(\Artify\Artify\Artifies\FacadeMakeCommand::class); //
		$this->commands(\Artify\Artify\Artifies\ResponseMakeCommand::class); //

		$this->commands(\Artify\Artify\Artifies\DatabaseCreateCommand::class); //
		$this->commands(\Artify\Artify\Artifies\UserAssignCommand::class); //

	}

	public function registerPublishables() {
		$publishablePath = __DIR__ . '/publishables';
		$this->publishes([
			$publishablePath . '/config/artify.php' => config_path('artify.php'),
			$publishablePath . '/migrations' => database_path('migrations'),
			$publishablePath . '/seeders' => database_path('seeds'),
		], 'artify');
	}
}
