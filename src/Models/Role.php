<?php

namespace Artify\Artify\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model {
	protected $fillable = [
        'permissions'
	];
    protected $casts = ['permissions' => 'array'];
	public function users() {
		return $this->belongsToMany(User::class, 'role_users', 'role_id', 'user_id');
	}

}
