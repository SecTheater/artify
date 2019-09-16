<?php

namespace Artify\Artify\Traits;

use Artify\Artify\Contracts\Models\Tenant;
use Artify\Artify\Models\TenantConnection;
use Illuminate\Support\Str;

trait IsTenant
{
    public static function boot()
    {
        parent::boot();

        static::creating(function ($tenant) {
            $tenant->uuid = (string) Str::uuid();
        });

        static::created(function ($tenant) {
            $tenant->tenantConnection()->save(static::newDatabaseConnection($tenant));
        });
    }

    public static function newDatabaseConnection(Tenant $tenant)
    {
        return new TenantConnection([
            'database' => Str::slug(config('app.name')) . '_' . $tenant->id,
        ]);
    }

    public function tenantConnection()
    {
        //static::getForeignKey();
        return $this->hasOne(TenantConnection::class, 'tenant_id', 'id');
    }
}
