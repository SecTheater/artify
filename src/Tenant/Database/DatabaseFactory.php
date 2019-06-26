<?php

namespace Artify\Artify\Tenant\Database;

use Artify\Artify\Contracts\Database\DatabaseAdapterInterface;
use Artify\Artify\Contracts\Models\Tenant;

class DatabaseFactory
{
    private $adapter;

    public function __construct(DatabaseAdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }
    public function make($connection)
    {
        return $this->adapter->make($connection);
    }
    public function create(Tenant $tenant)
    {
        return $this->adapter->make($tenant)->create();
    }
    public function exists(Tenant $tenant)
    {
        return $this->adapter->make($tenant)->exists();
    }
    public function drop(Tenant $tenant)
    {
        $this->adapter->make($tennat)->drop();
    }
}
