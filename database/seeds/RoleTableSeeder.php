<?php

use Illuminate\Database\Seeder;

use ICT\Permission;
use ICT\Role;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create([
            'name' => 'Admin',
            'slug' => 'admin'
        ])->permissions()->sync(Permission::all()->lists('id')->toArray());
        Role::create([
            'name' => 'Editor',
            'slug' => 'editor'
        ]);
    }
}
