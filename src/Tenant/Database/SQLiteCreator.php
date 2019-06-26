<?php

namespace Artify\Artify\Tenant\Database;

use Illuminate\Filesystem\Filesystem;

class MySQLCreator extends BaseSQLCreator
{
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
