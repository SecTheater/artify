<?php

namespace Artify\Artify\Tenant\Database;

use Illuminate\Filesystem\Filesystem;

class SQLiteCreator extends BaseSQLCreator
{
    public function create()
    {
        return app(Filesystem::class)->put(
            sprintf('%s.sqlite', database_path($this->tenant->tenantConnection->database)),
            ''
        );
    }
    public function exists()
    {
        return app(Filesystem::class)->exists(
            sprintf('%s.sqlite', database_path($this->tenant->tenantConnection->database))
        );
    }
    public function drop()
    {
        return app(Filesystem::class)->delete(
            sprintf('%s.sqlite', database_path($this->tenant->tenantConnection->database))
        );
    }
}
