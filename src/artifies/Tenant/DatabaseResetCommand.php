<?php
namespace Artify\Artify\Artifies\Tenant;

use Artify\Artify\Tenant\Database\DatabaseManager;
use Artify\Artify\Traits\AcceptsMultipleTenants;
use Artify\Artify\Traits\FetchesTenants;
use Illuminate\Database\Console\Migrations\ResetCommand;
use Illuminate\Database\Migrations\Migrator;

class DatabaseResetCommand extends ResetCommand
{
    use AcceptsMultipleTenants, FetchesTenants;

    protected $description = 'run reset migrations for tenants';

    public function __construct(Migrator $migrator, DatabaseManager $db)
    {
        parent::__construct($migrator);
        $this->setName('tenants:migrate:reset');
        $this->specifyParameters();
        $this->db = $db;
    }
    public function handle()
    {
        if (!$this->confirmToProceed()) {
            return;
        }
        if (!config('artify.tenant')) {
            $tenant = $this->ask('Please Setup the tenant model');
            config()->set('artify.tenant', $tenant);
        }
        $this->input->setOption('database', 'tenant');
        $this->tenants($this->option('tenants'))->each(function ($tenant) {
            $this->db->createConnection($tenant);
            $this->db->connectToTenant();
            parent::handle();
            $this->db->purge();
        });
    }
    protected function getMigrationPaths()
    {
        return [database_path('migrations/tenant')];
    }
}
