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
        Role::create(['name' => 'user']);

        \App\Models\User::factory()->create([
            'name' => 'Nazmus Sakib',
            'email' => 'engrsakibcse@gmail.com',
            'cid' => '1233',
        ])->assignRole('admin');
    }
}
