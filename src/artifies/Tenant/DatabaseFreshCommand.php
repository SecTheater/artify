<?php

namespace Artify\Artify\Artifies\Tenant;

use Artify\Artify\Tenant\Database\DatabaseManager;
use Artify\Artify\Traits\AcceptsMultipleTenants;
use Artify\Artify\Traits\FetchesTenants;
use Illuminate\Database\Console\Migrations\FreshCommand;

class DatabaseFreshCommand extends FreshCommand {

	use AcceptsMultipleTenants, FetchesTenants;

	protected $description = 'run fresh migrations for tenants';

	public function __construct(DatabaseManager $db) {
		parent::__construct();
		$this->setName('tenants:migrate:fresh');
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
				if ($this->option('drop-views')) {
					$this->dropAllViews($database);

					$this->info('Dropped all views successfully.');
				}

				$this->dropAllTables($database);

				$this->info('Dropped all tables successfully.');

				$this->call('tenants:migrate', [
					'--database' => $database,
					'--force' => true,
					'--step' => $this->option('step'),
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