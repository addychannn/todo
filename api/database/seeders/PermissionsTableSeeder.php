<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            //User Management
            [
                'name' => 'none',
                'description' => 'No Permission',
            ],
            [
                'name' => 'users_add',
                'description' => 'Add new user',
            ],
            [
                'name' => 'users_list',
                'description' => 'Show users table',
            ],
            [
                'name' => 'users_edit-profile',
                'description' => 'Update users profile',
            ],
            [
                'name' => 'users_edit-account',
                'description' => 'Update users account credentials',
            ],
            [
                'name' => 'users_edit-permission',
                'description' => 'Update users Roles and/or permissions',
            ],
            [
                'name' => 'users_change-status',
                'description' => 'Activate or deactivate users\' account',
            ],
            [
                'name' => 'users_give-direct-permissions',
                'description' => 'Give user direct permission to gain explicit access to a function',
            ],
            
            // User Roles
            [
                'name' => 'roles_list',
                'description' => 'Show Roles table',
            ],
            [
                'name' => 'roles_add',
                'description' => 'Add new Role',
            ],
            [
                'name' => 'roles_edit',
                'description' => 'Edit existing roles',
            ],
            [
                'name' => 'roles_delete',
                'description' => 'Delete roles (Warning! this action is permanent and non recoverable.)',
            ],

            // Account management
            [
                'name' => 'self_update-profile',
                'description' => 'Allow users to change their profile information',
            ],
            [
                'name' => 'self_update-account',
                'description' => 'Allow users to change their account information',
            ],
            [
                'name' => 'self_change-password',
                'description' => 'Allow users to change their account password',
            ],
            [
                'name' => 'self_change-avatar',
                'description' => 'Allow users to change their profile image',
            ],
        ];

        foreach($permissions as $permission) {
            Permission::create([
                'name' => $permission['name'],
                'description' => $permission['description'],
                'guard_name' => (bool)env('APP_AUTH_TOKEN', false) ? 'api' : 'sanctum'
            ]);
        }
    }
}
