<?php

namespace Artify\Artify\Tenant\Database;

use Artify\Artify\Contracts\Models\Tenant;
use Illuminate\Database\DatabaseManager as BaseDatabaseManager;

class DatabaseManager {
	protected $db, $factory;
	public function __construct(BaseDatabaseManager $db, DatabaseFactory $factory) {
		$this->db = $db;
		$this->factory = $factory;
	}
	public function createConnection(Tenant $tenant) {
		config()->set('database.connections.tenant', $this->getTenantConnection($tenant));
	}
	public function createDatabase(Tenant $tenant) {
		return $this->factory->create($tenant);
	}
	public function dropDatabase(Tenant $tenant) {
		return $this->factory->drop($tennat);
	}
	public function connectToTenant() {
		$this->db->reconnect('tenant');
	}
	public function purge() {
		$this->db->purge('tenant');
	}
	public function getDefaultConnectionName() {
		return config('database.default');
	}
	protected function getMigrationPath() {
		return database_path('migrations/' . config()->get($this->getConfigConnectionPath())['path'] ?? 'tenant');
	}
	protected function getTenantConnection(Tenant $tenant) {
		return array_merge(
			config()->get($this->getConfigConnectionPath()), $tenant->tenantConnection->only('database')
		);
	}
	public function hasDatabase(Tenant $tenant) {
		return $this->factory->exists($tenant);
	}

	protected function getConfigConnectionPath() {
		return sprintf('database.connections.%s', $this->getDefaultConnectionName());
	}
}