<?php

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Bikin tags
        $tags = [
            "game",
            "game android",
            "game pc",
            "game online",
            "game offline",
            "mobile game",
            "review game",
            "tips game",
            "berita game",
            "game terbaru",
            "tutorial",
            "teknologi",
            "android",
            "ios",
            "pc",
            "laptop",
            "software",
            "aplikasi",
            "gadget",
            "internet",
            "berita teknologi",
            "review produk",
            "rekomendasi",
            "update",
            "tutorial android",
            "gaming",
            "esports",
            "trending",
            "artikel populer",
            "media sosial"
        ];

        foreach ($tags as $tag) {
            Tag::firstOrCreate([
                'name' => $tag,
                'slug' => Str::slug($tag)
            
            ]);
        }

        // bikin categories
        $categories = [
            "Game",
            "Teknologi",
            "Gadget",
            "Aplikasi",
            "Android",
            "iOS",
            "Komputer",
            "Internet",
            "Berita",
            "Review",
            "Tips & Trik",
            "Tutorial",
            "Hardware",
            "Software",
            "Esports",
            "Mobile Game",
            "PC Game",
            "Kesehatan",
            "Edukasi",
            "Lifestyle",
            "Travel",
            "Kuliner",
            "Film",
            "Musik",
            "Anime",
            "Fotografi",
            "Video Editing",
            "Desain Grafis",
            "Media Sosial",
            "Startup",
            "Hobi",
            "Comparison",
            "Lainnya",
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate([
                'name' => $category,
                'slug' => Str::slug($category),
                'description' => 'ini'. $category
            
            ]);
        }

        
    }
}
