<?php

namespace Artify\Artify\Contracts\Models;

interface Role {
	public function hasRole($role);
	public function hasAllRole($roles);
	public function hasAnyRole($roles);
	public function removePermission(...$permission);
	public function updatePermission($permission, $value = true, $create = false);
	public function addPermission($permission, $value = true);
	public function inRole($slug);
}