<?php
namespace Artify\Artify\Tenant\Listeners;
use Artify\Artify\Tenant\Database\DatabaseManager;
use Artify\Artify\Tenant\Events\TenantIdentified;
use Artify\Artify\Tenant\Manager;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
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
        app(Manager::class)->setTenant($event->tenant);
        $this->db->createConnection($event->tenant);
    }
}
