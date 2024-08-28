<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

use App\Models\User;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            // [
            //     'username' => 'superadmin',
            //     'email' => 'super@admin.com',
            //     'password' => Hash::make('superadmin'),
            //     'role' => 'Admin'
            // ],
            // [
            //     'username' => 'admin123',
            //     'email' => 'admin@admin.com',
            //     'password' => Hash::make('admin123'),
            //     'role' => 'Moderator',
            // ],
            [
                'username' => 'whykhamist',
                'email' => null,
                'password' => Hash::make('wasd1234'),
                'role' => 'Admin',
            ]
        ];

        foreach($users as $user) {
            $new_user = User::create([
                'username' => $user['username'],
                'email' => $user['email'],
                'email_verified_at' => now(),
                'password' => $user['password'],
            ]);

            // $new_user->assignRole($user['role']);
            $role = Role::where('name', $user['role'])->first();
            DB::table('model_has_roles')->insert([
                'role_id' => $role->id,
                'model_type' => 'App\Models\User',
                'model_id' => $new_user->id,
            ]);
        }
    }
}
