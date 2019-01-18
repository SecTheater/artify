<?php
namespace Artify\Artify\Tenant\Database;
use Artify\Artify\Contracts\Models\Tenant;
use DB;

class PostgresCreator extends BaseSQLCreator {
	public function create() {
		return DB::statement("
            CREATE DATABASE {$this->tenant->tenantConnection->database}
        ");
	}
	public function exists() {
		return DB::select("select exists( SELECT datname FROM pg_catalog.pg_database WHERE lower(datname) = lower('{$this->tenant->tenantConnection->database}')
        );")[0]->exists;
	}
	public function drop() {
		return DB::statement("DROP DATABASE " . $this->tenant->tenantConnection->database);
	}
}
