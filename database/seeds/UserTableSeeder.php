<?php

use Illuminate\Database\Seeder;

use ICT\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'User Dude',
            'email' => 'user@user.com',
            'password' => 'asdf',
            'role_id' => 1
        ]);
    }
}
