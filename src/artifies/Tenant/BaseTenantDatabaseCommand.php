<?php

namespace Artify\Artify\Artifies\Tenant;

use Artify\Artify\Traits\AcceptsMultipleTenants;
use Artify\Artify\Traits\FetchesTenants;

trait BaseTenantDatabaseCommand {
	use AcceptsMultipleTenants, FetchesTenants;
	protected $db;
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

			if (!$this->db->hasDatabase($tenant)) {
				$this->db->createDatabase($tenant);
			}
			$this->db->createConnection($tenant);
			$this->db->connectToTenant();
			parent::handle();
			$this->db->purge();
		});
	}
	protected function getMigrationPaths() {
		return [database_path('migrations/tenant')];
	}
}