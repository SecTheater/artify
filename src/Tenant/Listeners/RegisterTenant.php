<?php
namespace Artify\Artify\Tenant\Listeners;

use Artify\Artify\Tenant\Database\DatabaseManager;
use Artify\Artify\Tenant\Events\TenantIdentified;
use Artify\Artify\Tenant\Manager;

class RegisterTenant
{
    protected $db;
    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }
    /**
     * Handle the event.
     *
     * @param  TenantIdentified  $event
     * @return void
     */
    public function handle(TenantIdentified $event)
    {
        if (!session('tenant')) {
            session(['tenant' => $event->tenant->{$event->tenant->getRouteKeyName()}]);
        }

        app(Manager::class)->setTenant($event->tenant);
        $this->db->createConnection($event->tenant);
    }
}
