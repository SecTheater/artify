<?php

namespace Artify\Artify\Artifies\Tenant;

use Artify\Artify\Tenant\Database\DatabaseManager;
use Artify\Artify\Traits\AcceptsMultipleTenants;
use Artify\Artify\Traits\FetchesTenants;
use Illuminate\Database\Console\Migrations\RefreshCommand;
use Illuminate\Database\Migrations\Migrator;

class DatabaseRefreshCommand extends RefreshCommand {
	use AcceptsMultipleTenants, FetchesTenants;

	protected $description = 'Reset and re-run all migrations for tenants';

	public function __construct(Migrator $migrator, DatabaseManager $db) {
		parent::__construct($migrator);
		$this->setName('tenants:migrate:refresh');
		$this->specifyParameters();
		$this->db = $db;
	}
	public function handle() {
		if (!$this->confirmToProceed()) {
			return;
		}

		if (!config('artify.tenant')) {
			$tenant = $this->ask('Please Setup the tenant model');
			config()->set('artify.tenant', $tenant);
		}
		$this->input->setOption('database', 'tenant');
		$this->tenants($this->option('tenants'))->each(function ($tenant) {
			if ($this->db->hasDatabase($tenant)) {
				$this->db->createConnection($tenant);
				$this->db->connectToTenant();
				$database = $this->input->getOption('database');

				$path = $this->input->getOption('path');

				$force = $this->input->getOption('force');

				// If the "step" option is specified it means we only want to rollback a small
				// number of migrations before migrating again. For example, the user might
				// only rollback and remigrate the latest four migrations instead of all.
				$step = $this->input->getOption('step') ?: 0;

				if ($step > 0) {
					$this->call('tenants:migrate:rollback', [
						'--database' => $database,
						'--force' => $force,
						'--path' => $path,
					]);
				} else {
					$this->call('tenants:migrate:reset', [
						'--database' => $database,
						'--force' => $force,
						'--path' => $path,

					]);
				}

				// The refresh command is essentially just a brief aggregate of a few other of
				// the migration commands and just provides a convenient wrapper to execute
				// them in succession. We'll also see if we need to re-seed the database.
				$this->call('tenants:migrate', [
					'--database' => $database,
					'--path' => $path,
					'--realpath' => $this->input->getOption('realpath'),
					'--force' => $force,
				]);

				if ($this->needsSeeding()) {
					$this->call('tenants:seed', [
						'--database' => $database,
						'--class' => $this->option('seeder') ?: 'DatabaseSeeder',
						'--force' => $this->option('force'),
					]);
				}
				$this->db->purge();
			}
		});
	}

}