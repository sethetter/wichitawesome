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
            'name' => 'Christian Taylor',
            'email' => 'christianbtaylor@gmail.com',
            'password' => bcrypt('asdf'),
            'role_id' => 1
        ]);
    }
}
