<?php

namespace Artify\Artify\Traits;

trait FetchesTenants {
	public function tenants($ids = null) {
		$tenantModel = config('artify.tenant');
		$tenants = $tenantModel::query();

		if ($ids) {
			$tenants = $tenants->whereIn('id', $ids);
		}

		return $tenants;
	}
}
