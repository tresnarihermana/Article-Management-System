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
        $descriptions = [
            "Game" => "Berisi berita, review, dan tips seputar berbagai jenis permainan.",
            "Teknologi" => "Informasi terbaru mengenai perkembangan teknologi dan inovasi digital.",
            "Gadget" => "Ulasan dan rekomendasi gadget terbaru, mulai dari smartphone hingga wearable.",
            "Aplikasi" => "Tips, review, dan tutorial aplikasi untuk berbagai platform.",
            "Android" => "Berita dan tips khusus seputar sistem operasi Android dan aplikasinya.",
            "iOS" => "Informasi dan tutorial untuk pengguna perangkat Apple dan iOS.",
            "Komputer" => "Artikel seputar hardware, software, dan tips penggunaan komputer.",
            "Internet" => "Berita dan panduan terkait dunia internet, jaringan, dan keamanan online.",
            "Berita" => "Informasi terbaru dari berbagai topik populer di Indonesia dan dunia.",
            "Review" => "Ulasan mendalam tentang produk, aplikasi, game, film, dan teknologi.",
            "Tips & Trik" => "Panduan praktis dan tips untuk mempermudah aktivitas sehari-hari.",
            "Tutorial" => "Langkah-langkah tutorial lengkap untuk berbagai bidang dan software.",
            "Hardware" => "Info dan ulasan mengenai komponen komputer dan perangkat keras lainnya.",
            "Software" => "Review dan panduan software untuk PC, smartphone, dan profesional.",
            "Esports" => "Berita dan turnamen seputar dunia esports dan kompetisi game online.",
            "Mobile Game" => "Ulasan, tips, dan berita seputar game mobile terbaru.",
            "PC Game" => "Berita dan review game PC serta tips bermain yang menarik.",
            "Kesehatan" => "Tips kesehatan, olahraga, dan gaya hidup sehat untuk semua usia.",
            "Edukasi" => "Konten pembelajaran, kursus online, dan tips belajar efektif.",
            "Lifestyle" => "Artikel tentang gaya hidup, fashion, dan tren terkini.",
            "Travel" => "Panduan perjalanan, destinasi populer, dan tips liburan seru.",
            "Kuliner" => "Resep, review makanan, dan rekomendasi tempat makan enak.",
            "Film" => "Berita, review, dan rekomendasi film terbaru dan klasik.",
            "Musik" => "Update musik, ulasan album, dan profil artis favorit.",
            "Anime" => "Berita, review, dan rekomendasi anime terbaru dan populer.",
            "Fotografi" => "Tips, tutorial, dan inspirasi dunia fotografi dan editing foto.",
            "Video Editing" => "Panduan dan tips editing video menggunakan berbagai software.",
            "Desain Grafis" => "Tutorial, inspirasi, dan review software desain grafis.",
            "Media Sosial" => "Tips, tren, dan strategi penggunaan platform media sosial.",
            "Startup" => "Informasi, tips, dan berita seputar dunia startup dan bisnis digital.",
            "Hobi" => "Ide dan tips untuk mengembangkan berbagai hobi dan kegiatan kreatif.",
            "Comparison" => "Perbandingan produk, gadget, atau aplikasi untuk membantu memilih.",
            "Lainnya" => "Konten lain yang tidak termasuk dalam kategori utama di atas."
        ];
        foreach ($categories as $category) {
            Category::firstOrCreate([
                'name' => $category,
                'slug' => Str::slug($category),
                'description' => $descriptions[$category] ?? "Deskripsi belum tersedia."

            ]);
        }
        Article::factory()
            ->count(200)
            ->create()
            ->each(function ($article) {
                // random 1–3 kategori
                $article->categories()->attach(
                    Category::inRandomOrder()->limit(rand(1, 3))->pluck('id')
                );

                // random 2–5 tag
                $article->tags()->attach(
                    Tag::inRandomOrder()->limit(rand(2, 5))->pluck('id')
                );
            });
    }
}
