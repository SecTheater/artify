<?php

use App\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = config('artify.models.namespace').config('artify.models.role');
        $role = new $role();
        $admin = $role->create([
            'slug'        => 'admin',
            'name'        => 'Administrator',
            'permissions' => [
                'view-post'       => true,
                'update-post'     => true,
                'delete-post'     => true,
                'create-post'     => true,
                'approve-post'    => true,
                'create-tag'      => true,
                'update-tag'      => true,
                'delete-tag'      => true,
                'view-tag'        => true,
                'view-comment'    => true,
                'update-comment'  => true,
                'delete-comment'  => true,
                'create-comment'  => true,
                'approve-comment' => true,
                'view-reply'      => true,
                'update-reply'    => true,
                'delete-reply'    => true,
                'create-reply'    => true,
                'approve-reply'   => true,
                'upgrade-user'    => true,
                'downgrade-user'  => true,
            ],
        ]);
        $moderator = $role->create([
            'slug'        => 'moderator',
            'name'        => 'Moderator',
            'permissions' => [
                'create-post'     => true,
                'view-post'       => true,
                'delete-post'     => true,
                'update-post'     => true,
                'approve-post'    => false,
                'create-tag'      => true,
                'update-tag'      => true,
                'view-tag'        => true,
                'delete-tag'      => true,
                'create-comment'  => true,
                'update-comment'  => true,
                'delete-comment'  => true,
                'view-comment'    => true,
                'approve-comment' => true,
                'create-reply'    => true,
                'update-reply'    => true,
                'delete-reply'    => true,
                'view-reply'      => true,
                'approve-reply'   => true,
                'upgrade-user'    => false,
                'downgrade-user'  => false,
            ],
        ]);
        $user = $role->create([
            'name'        => 'Normal User',
            'slug'        => 'user',
            'permissions' => [
                'create-post'     => false,
                'view-post'       => true,
                'update-post'     => false,
                'delete-post'     => false,
                'approve-post'    => false,
                'create-tag'      => false,
                'view-tag'        => false,
                'update-tag'      => false,
                'delete-tag'      => false,
                'create-comment'  => true,
                'update-comment'  => true,
                'view-comment'    => true,
                'delete-comment'  => true,
                'approve-comment' => false,
                'create-reply'    => true,
                'update-reply'    => true,
                'view-reply'      => true,
                'delete-reply'    => true,
                'approve-reply'   => false,
                'upgrade-user'    => false,
                'downgrade-user'  => false,
            ],
        ]);
    }
}
