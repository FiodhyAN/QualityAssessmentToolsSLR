<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        DB::table('users')->insert([
            'name' => 'Super Admin',
            'username' => 'superadmin',
            'is_superAdmin' => true,
            'password' => bcrypt('superadmin'),
        ]);
        DB::table('users')->insert([
            'name' => 'Admin',
            'username' => 'admin',
            'is_admin' => true,
            'password' => bcrypt('admin'),
        ]);
        DB::table('users')->insert([
            'name' => 'Reviewer',
            'username' => 'reviewer',
            'is_reviewer' => true,
            'password' => bcrypt('reviewer'),
        ]);
    }
}
