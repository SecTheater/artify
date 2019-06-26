<?php
namespace Artify\Artify\Artifies\Tenant;

use Artify\Artify\Tenant\Database\DatabaseManager;
use Illuminate\Database\Console\Migrations\RollbackCommand;
use Illuminate\Database\Migrations\Migrator;

class DatabaseRollbackCommand extends RollbackCommand
{
    use BaseTenantDatabaseCommand;

    protected $description = 'Rollback migrations for tenants';

    public function __construct(Migrator $migrator, DatabaseManager $db)
    {
        parent::__construct($migrator);
        $this->specifyParameters();
        $this->setName('tenants:rollback');
        $this->db = $db;
    }
}
