<?php
namespace Artify\Artify\Tenant\Events;

use Artify\Artify\Contracts\Models\Tenant;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TenantIdentified
{
    use Dispatchable, SerializesModels;
    public $tenant;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
    }
}
