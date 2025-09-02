<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Like;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition(): array
    {
        $title = $this->faker->sentence(6, true);
        $paragraphs = $this->faker->paragraphs(10);
        $body = '';
        foreach ($paragraphs as $i => $para) {
            if ($i % 3 === 0) {
                $body .= "<h2>" . $this->faker->sentence(4) . "</h2>";
            }
            $body .= "<p>$para</p>";
        }

        return [
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory()->create()->assignRole('user')->id,
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::random(5),
            'excerpt' => Str::limit(strip_tags($paragraphs[0]), 150),
            'body' => $body,
            'cover' => '/cover/bBEyfSKKtUrUgWa56G2CB47usnV1qQ4QerT6wAEY.jpg',
            'featured_image' => $this->faker->imageUrl(800, 400, 'tech', true),
            'status' => $this->faker->randomElement(['draft', 'published']),
            'is_pinned' => $this->faker->boolean(10),
            'published_at' => now(),
            'views' => $this->faker->numberBetween(0, 35600),
            'hits' => $this->faker->numberBetween(0, 35700),
            'created_at' => $this->faker->dateTimeBetween('-2 years', 'now'),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function ($article) {
            $users = User::inRandomOrder()->take(rand(1, 10))->get();
            foreach ($users as $user) {
                \App\Models\Like::factory()->create([
                    'article_id' => $article->id,
                    'user_id' => $user->id,
                ]);
            }
        });
    }
}
