<?php

namespace Artify\Artify\Models;

use Artify\Artify\Contracts\Models\Role as RoleInterface;
use Artify\Artify\Models\Role;
use Artify\Artify\Traits\Roles\Roles;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements RoleInterface
{
    use Roles;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_users', 'user_id', 'role_id');
    }
}
