<?php

namespace Artify\Artify\Contracts\Models;

interface Tenant {
	public function getConnectionName();
	public static function newDatabaseConnection(Tenant $tenant);
}