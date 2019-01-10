<?php
namespace Artify\Artify\Traits;

trait ForSystem {
	public function getConnectionName() {
		return 'pgsql';
	}
}