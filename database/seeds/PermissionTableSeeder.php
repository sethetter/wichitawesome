<?php

use Illuminate\Database\Seeder;

use ICT\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'users.admin']);
        Permission::create(['name' => 'users.update']);
        Permission::create(['name' => 'users.destroy']);
        Permission::create(['name' => 'permissions.admin']);
        Permission::create(['name' => 'permissions.update']);
        Permission::create(['name' => 'permissions.destroy']);
        Permission::create(['name' => 'roles.admin']);
        Permission::create(['name' => 'roles.update']);
        Permission::create(['name' => 'roles.destroy']);
        Permission::create(['name' => 'venues.admin']);
        Permission::create(['name' => 'venues.update']);
        Permission::create(['name' => 'venues.destroy']);
        Permission::create(['name' => 'events.admin']);
        Permission::create(['name' => 'events.update']);
        Permission::create(['name' => 'events.destroy']);
        Permission::create(['name' => 'organizations.admin']);
        Permission::create(['name' => 'organizations.update']);
        Permission::create(['name' => 'organizations.destroy']);
        Permission::create(['name' => 'tags.admin']);
        Permission::create(['name' => 'tags.update']);
        Permission::create(['name' => 'tags.destroy']);
    }
}
