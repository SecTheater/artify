<?php
namespace Artify\Artify\Traits;

trait ForTenant
{
    public function getConnectionName()
    {
        return 'tenant';
    }
}
