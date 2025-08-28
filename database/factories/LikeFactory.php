<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Like;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LikeFactory extends Factory
{
    protected $model = Like::class;

    public function definition(): array
    {
        return [
            'article_id' => Article::factory(),
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
        ];
    }
}
