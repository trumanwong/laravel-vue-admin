<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Acl;

class SetupRolePermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (Acl::roles() as $role) {
            Role::findOrCreate($role);
        }

        $adminRole = Role::findByName(Acl::ROLE_ADMIN);
        $managerRole = Role::findByName(Acl::ROLE_MANAGER);
        $editorRole = Role::findByName(Acl::ROLE_EDITOR);
        $userRole = Role::findByName(Acl::ROLE_USER);
        $visitorRole = Role::findByName(Acl::ROLE_VISITOR);

        foreach (Acl::permissions() as $permission) {
            Permission::findOrCreate($permission, 'api');
        }

        // Setup basic permission
        $adminRole->givePermissionTo(Acl::permissions());
        $managerRole->givePermissionTo(Acl::permissions([Acl::PERMISSION_PERMISSION_MANAGE]));
        $editorRole->givePermissionTo(Acl::menuPermissions());
        $userRole->givePermissionTo([
            Acl::PERMISSION_VIEW_MENU_PERMISSION,
        ]);
        $visitorRole->givePermissionTo([
            Acl::PERMISSION_VIEW_MENU_PERMISSION,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('editor');
            });
        }

        /** @var \App\User[] $users */
        $users = \App\Laravue\Models\User::all();
        foreach ($users as $user) {
            $roles = array_reverse(Acl::roles());
            foreach ($roles as $role) {
                if ($user->hasRole($role)) {
                    $user->role = $role;
                    $user->save();
                }
            }
        }
    }
}
