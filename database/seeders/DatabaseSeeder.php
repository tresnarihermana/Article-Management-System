<?php

namespace Database\Seeders;

use App\Models\Tag;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\Article;
use App\Models\Comment;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Database\Seeders\ArticleSeeder;
use Database\Seeders\RolePermissionSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            ArticleSeeder::class
        ]);
        // User::factory(10)->create();
        $Sadmin = User::factory()->create([
            'name' => 'SuperAdmin',
            'username' => 'Sadmin',
            'email' => 'sadmin@gmail.com',
            'email_verified_at' => now(),
        ]);
        $Sadmin->assignRole('Super Admin');

        $admin = User::factory()->create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        $operator = User::factory()->create([
            'name' => 'Operator',
            'username' => 'operator',
            'email' => 'operator@gmail.com',
            'email_verified_at' => now(),
        ]);
        $operator->assignRole('operator');

        $user = User::factory()->create([
            'name' => 'First User',
            'username' => 'user',
            'email' => 'user@gmail.com',
            'email_verified_at' => now(),
        ]);
        $user->assignRole('user');




        User::factory(100)->create()->each(function ($user) {
            $user->assignRole('user');
        });
    }
}
