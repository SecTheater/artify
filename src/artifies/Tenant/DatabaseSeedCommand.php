<?php
namespace Artify\Artify\Artifies\Tenant;

use Artify\Artify\Tenant\Database\DatabaseManager;
use Illuminate\Database\ConnectionResolverInterface as Resolver;
use Illuminate\Database\Console\Seeds\SeedCommand;

class DatabaseSeedCommand extends SeedCommand {
	use BaseTenantDatabaseCommand;
	protected $description = 'Seeds tenant databases';

	public function __construct(Resolver $resolver, DatabaseManager $db) {
		parent::__construct($resolver);
		$this->setName('tenants:seed');
		$this->specifyParameters();
		$this->db = $db;
	}
}