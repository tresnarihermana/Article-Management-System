<?php

namespace Database\Seeders;

use App\Models\Tag;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\Article;
use App\Models\Comment;
use App\Models\Category;
use Illuminate\Database\Seeder;
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

     $tags = \App\Models\Tag::factory(10)->create();
    $categories = \App\Models\Category::factory(5)->create();
       $articles = Article::factory(10)->create()->each(function ($article) use ($tags, $categories) {
    $article->tags()->attach($tags->random(rand(1, 3))->pluck('id'));
    $article->categories()->attach($categories->random(rand(1, 2))->pluck('id'));
});



        User::factory(100)->create()->each(function ($user) {
            $user->assignRole('user');
        });
    }
}
