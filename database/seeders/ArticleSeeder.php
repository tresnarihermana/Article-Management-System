<?php

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Article;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
// 1. Bikin tags
$tags = [
    "game", "game android", "game pc", "game online", "game offline",
    "mobile game", "review game", "tips game", "berita game", "game terbaru",
    "tutorial", "teknologi", "android", "ios", "pc", "laptop", "software",
    "aplikasi", "gadget", "internet", "berita teknologi", "review produk",
    "rekomendasi", "update", "tutorial android", "gaming", "esports",
    "trending", "artikel populer", "media sosial"
];

foreach ($tags as $tag) {
    Tag::firstOrCreate([
        'name' => $tag,
        'slug' => Str::slug($tag),
    ]);
}

// 2. Bikin categories
$categories = [
    "Game",
    "Teknologi",
    "Berita",
    "Review",
    "Tips & Trik",
    "Tutorial",
    "Lifestyle",
    "Anime",
    "Hobby",
    "Comparison",
    "Lainnya",
];

$descriptions = [
    "Game" => "Berisi berita, review, dan tips seputar berbagai jenis permainan.",
    "Teknologi" => "Informasi terbaru mengenai perkembangan teknologi dan inovasi digital.",
    "Berita" => "Informasi terbaru dari berbagai topik populer di Indonesia dan dunia.",
    "Review" => "Ulasan mendalam tentang produk, aplikasi, game, film, dan teknologi.",
    "Tips & Trik" => "Panduan praktis dan tips untuk mempermudah aktivitas sehari-hari.",
    "Tutorial" => "Langkah-langkah tutorial lengkap untuk berbagai bidang dan software.",
    "Lifestyle" => "Artikel tentang gaya hidup, fashion, dan tren terkini.",
    "Anime" => "Berita, review, dan rekomendasi anime terbaru dan populer.",
    "Hobby" => "Ide dan tips untuk mengembangkan berbagai hobi dan kegiatan kreatif.",
    "Comparison" => "Perbandingan produk, gadget, atau aplikasi untuk membantu memilih.",
    "Lainnya" => "Konten lain yang tidak termasuk dalam kategori utama di atas."
];

foreach ($categories as $category) {
    Category::firstOrCreate([
        'name' => $category,
        'slug' => Str::slug($category),
        'description' => $descriptions[$category] ?? "Deskripsi belum tersedia.",
    ]);
}

// 3. Mapping pivot category_tag
$pivot = [
    "Game" => ["game", "game android", "game pc", "game online", "game offline", "mobile game", "review game", "tips game", "berita game", "game terbaru", "gaming", "esports"],
    "Teknologi" => ["teknologi", "android", "ios", "pc", "laptop", "software", "aplikasi", "gadget", "internet", "berita teknologi", "media sosial"],
    "Berita" => ["berita game", "berita teknologi", "trending", "artikel populer", "update"],
    "Review" => ["review game", "review produk", "rekomendasi"],
    "Tips & Trik" => ["tips game", "tutorial android", "rekomendasi"],
    "Tutorial" => ["tutorial", "tutorial android", "software", "aplikasi"],
    "Lifestyle" => ["media sosial", "trending", "artikel populer"],
    "Anime" => ["anime"], // bisa tambah lagi
    "Hobby" => ["gaming", "esports", "artikel populer"],
    "Comparison" => ["review produk", "rekomendasi"],
    "Lainnya" => ["update", "artikel populer"],
];

foreach ($pivot as $categoryName => $tagNames) {
    $category = Category::where('name', $categoryName)->first();
    if (!$category) {
        echo "Category {$categoryName} tidak ditemukan\n";
        continue;
    }

    $tagIds = Tag::whereIn('name', $tagNames)->pluck('id')->toArray();

    if (empty($tagIds)) {
        echo "Tidak ada tags cocok untuk category {$categoryName}\n";
        continue;
    }

    $category->tags()->syncWithoutDetaching($tagIds);

    echo "Category {$categoryName} berhasil dikaitkan dengan " . count($tagIds) . " tags.\n";
}


        // Article::factory()
        //     ->count(200)
        //     ->create()
        //     ->each(function ($article) {
        //         // random 1–3 kategori
        //         $article->categories()->attach(
        //             Category::inRandomOrder()->limit(rand(1, 3))->pluck('id')
        //         );

        //         // random 2–5 tag
        //         $article->tags()->attach(
        //             Tag::inRandomOrder()->limit(rand(2, 5))->pluck('id')
        //         );
        //     });
    }
}
