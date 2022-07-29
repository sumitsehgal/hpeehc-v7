<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminRole = Role::create(['name' => 'admin']);
        $stateRole = Role::create(['name' => 'state']);
        $siteRole = Role::create(['name' => 'site']);

        $addUserPermission = Permission::create(['name' => 'add users']);
        $editUserPermission = Permission::create(['name' => 'edit users']);
        $deleteUserPermission = Permission::create(['name' => 'delete users']);
        $listUserPermission = Permission::create(['name' => 'list users']);
        
        $adminRole->syncPermissions([
            $addUserPermission,
            $editUserPermission,
            $deleteUserPermission,
            $listUserPermission
        ]);
    }
}

