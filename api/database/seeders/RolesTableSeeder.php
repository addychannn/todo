<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                'name' => 'Admin',
                'protected' => 1,
                'description'=> 'A person with "Full/Unrestricted" access to admin features.',
                'permissions' => [],
            ],
            [
                'name' => 'Moderator',
                'protected' => 1,
                'description'=> 'A person with access to some admin features.',
                'permissions' => [
                    'users_add',
                    'users_list',
                    'users_edit-profile',
                    'users_edit-account',
                    'users_edit-permission',
                    'users_change-status',
                    'users_give-direct-permissions',

                    'roles_list',
                    'roles_add',
                    'roles_edit',
                    'roles_delete',
                ],
            ],
            [
                'name' => 'User',
                'protected' => 1,
                'description'=> 'A person with limited access to system features.',
                'permissions' => [
                    'self_change-password',
                    'self_change-avatar',
                    'self_update-profile',
                    'self_update-account',
                ],
            ],
        ];

        foreach($roles as $role) {
            $roleName = Role::create([
                'name' => $role['name'],
                'protected' => $role['protected'],
                'description' => $role['description'],
                'guard_name' => (bool)env('APP_AUTH_TOKEN', false) ? 'api' : 'sanctum'
            ]);
            $roleName->syncPermissions($role['permissions']);
        }
    }
}
