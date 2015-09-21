<?php

use Illuminate\Database\Seeder;

use ICT\Tag;

class TagTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Tag::create(['name' => '18+']);
        Tag::create(['name' => '21+']);
        Tag::create(['name' => 'art']);
        Tag::create(['name' => 'all ages']);
        Tag::create(['name' => 'comedy']);
        Tag::create(['name' => 'dance']);
        Tag::create(['name' => 'education']);
        Tag::create(['name' => 'gaming']);
        Tag::create(['name' => 'music']);
        Tag::create(['name' => 'no cover']);
        Tag::create(['name' => 'tech']);
        Tag::create(['name' => 'performance']);
    }
}
