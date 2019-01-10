<?php

namespace Artify\Artify\Artifies\Tenant;

use Artify\Artify\Tenant\Database\DatabaseManager;
use Illuminate\Database\Console\Migrations\MigrateCommand;
use Illuminate\Database\Migrations\Migrator;

class DatabaseMigrationCommand extends MigrateCommand {

	use BaseTenantDatabaseCommand;

	protected $description = 'run migrations for tenants';

	public function __construct(Migrator $migrator, DatabaseManager $db) {
		parent::__construct($migrator);
		$this->setName('tenants:migrate');
		$this->specifyParameters();
		$this->db = $db;
	}

}