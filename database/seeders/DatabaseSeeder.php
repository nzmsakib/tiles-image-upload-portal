<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        Role::create(['name' => 'admin']);
        Role::create(['name' => 'company']);

        \App\Models\User::factory()->create([
            'name' => 'Nazmus Sakib',
            'email' => 'engrsakibcse@gmail.com',
            'cid' => '1233',
        ])->assignRole('admin');

        \App\Models\User::factory()->create([
            'name' => 'Shopee Tiles',
            'email' => 'com1@gmail.com',
            'cid' => '1234',
        ])->assignRole('company');

        \App\Models\User::factory()->create([
            'name' => 'AB Ceramics',
            'email' => 'com2@gmail.com',
            'cid' => '1235',
        ])->assignRole('company');

        \App\Models\User::factory()->create([
            'name' => 'AC Ceramics',
            'email' => 'com3@gmail.com',
            'cid' => '1236',
        ])->assignRole('company');
    }
}
