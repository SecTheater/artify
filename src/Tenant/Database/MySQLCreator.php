<?php

namespace Artify\Artify\Tenant\Database;

class MySQLCreator extends BaseSQLCreator
{
    public function create()
    {
        return \DB::statement('CREATE DATABASE ' . $this->tenant->tenantConnection->database);
    }
    public function exists()
    {
        return \DB::statement("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = " . "'" . $this->tenant->tenantConnection->database . "'");
    }
    public function drop()
    {
        return \DB::statement("DROP DATABASE " . $this->tenant->tenantConnection->database);
    }
}
