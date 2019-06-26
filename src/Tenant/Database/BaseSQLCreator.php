<?php

namespace Artify\Artify\Tenant\Database;

use Artify\Artify\Contracts\Database\SQLDriverCreatorInterface;
use Artify\Artify\Contracts\Models\Tenant;

abstract class BaseSQLCreator implements SQLDriverCreatorInterface
{
    protected $tenant;
    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
    }
    abstract public function create();
    abstract public function exists();
}
