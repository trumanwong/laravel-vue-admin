<?php

namespace Database\Seeders;

use App\Models\Acl;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::query()->firstOrCreate([
            'name' => config('content.admin_name'),
        ], [
            'email' => config('content.admin_email'),
            'password' => Hash::make(config('content.admin_password')),
            'status' => true,
            'sex' => 0,
            'birthday' => '2006-01-02 15:04:05',
            'description' => 'Talk is cheap. Show me the code',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        $role = Role::findByName(Acl::ROLE_ADMIN);
        $user->syncRoles($role);
    }
}
