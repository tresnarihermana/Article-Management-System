<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Article;
use App\Models\Category;
use Carbon\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $startThisMonth = $now->copy()->startOfMonth();
        $endThisMonth   = $now->copy()->endOfMonth();

        $startLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endLastMonth   = $now->copy()->subMonth()->endOfMonth();

        // ==== TOTAL DATA ====
        $totalViews    = Article::sum('views');   // views diambil dari kolom article
        $totalUsers    = User::count();
        $totalAuthors  = User::whereHas('roles', fn($q) => $q->where('name', 'author'))->count();
        $totalArticles = Article::count();

        // ==== THIS MONTH ====
        $viewsThisMonth    = Article::whereBetween('created_at', [$startThisMonth, $endThisMonth])->sum('views');
        $usersThisMonth    = User::whereBetween('created_at', [$startThisMonth, $endThisMonth])->count();
        $authorsThisMonth  = User::whereHas('roles', fn($q) => $q->where('name', 'author'))
            ->whereBetween('created_at', [$startThisMonth, $endThisMonth])
            ->count();
        $articlesThisMonth = Article::whereBetween('created_at', [$startThisMonth, $endThisMonth])->count();

        // ==== LAST MONTH ====
        $viewsLastMonth    = Article::whereBetween('created_at', [$startLastMonth, $endLastMonth])->sum('views');
        $usersLastMonth    = User::whereBetween('created_at', [$startLastMonth, $endLastMonth])->count();
        $authorsLastMonth  = User::whereHas('roles', fn($q) => $q->where('name', 'author'))
            ->whereBetween('created_at', [$startLastMonth, $endLastMonth])
            ->count();
        $articlesLastMonth = Article::whereBetween('created_at', [$startLastMonth, $endLastMonth])->count();

        // ==== HITUNG GROWTH ====
        $growth = function ($thisMonth, $lastMonth) {
            if ($lastMonth === 0) {
                return $thisMonth > 0 ? 100 : 0;
            }
            return round((($thisMonth - $lastMonth) / $lastMonth) * 100, 2);
        };

        $stats = [
            [
                "title" => "Total Views",
                "value" => $totalViews,
                "percentage" => $growth($viewsThisMonth, $viewsLastMonth) . "%",
                "trend" => $growth($viewsThisMonth, $viewsLastMonth) >= 0 ? "up" : "down",
                "vs" => "vs last month",
                "icon" => "pi-eye",
                "color" => "bg-blue-100 text-blue-600",
            ],
            [
                "title" => "Total Users",
                "value" => $totalUsers,
                "percentage" => $growth($usersThisMonth, $usersLastMonth) . "%",
                "trend" => $growth($usersThisMonth, $usersLastMonth) >= 0 ? "up" : "down",
                "vs" => "vs last month",
                "icon" => "pi-users",
                "color" => "bg-green-100 text-green-600",
            ],
            [
                "title" => "Total New Author",
                "value" => $totalAuthors,
                "percentage" => $growth($authorsThisMonth, $authorsLastMonth) . "%",
                "trend" => $growth($authorsThisMonth, $authorsLastMonth) >= 0 ? "up" : "down",
                "vs" => "vs last month",
                "icon" => "pi-user-plus",
                "color" => "bg-orange-100 text-orange-600",
            ],
            [
                "title" => "Articles",
                "value" => $totalArticles,
                "percentage" => $growth($articlesThisMonth, $articlesLastMonth) . "%",
                "trend" => $growth($articlesThisMonth, $articlesLastMonth) >= 0 ? "up" : "down",
                "vs" => "vs last month",
                "icon" => "pi-book",
                "color" => "bg-purple-100 text-purple-600",
            ],
        ];

        // Kalau kamu juga butuh data chart (misalnya spline chart)
        $articlesData = Article::selectRaw('DATE(created_at) as date, SUM(views) as total_views, SUM(hits) as total_hits')
            ->whereBetween('created_at', [$startThisMonth->copy()->subMonths(6), $endThisMonth])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $categoriesData = Category::withCount('articles')->get()
            ->map(fn($cat) => [
                'name' => $cat->name,
                'total_views' => $cat->articles->sum('views'),
            ]);

        $topArticles = Article::with('categories')
            ->orderByDesc('views')
            ->take(6) // ambil 6 artikel teratas
            ->get()
            ->map(fn($a) => [
                'title' => $a->title,
                'category' => $a->categories->pluck('name')->join(', ') ?? 'Uncategorized',
                'views' => $a->views,
            ]);

        // total views semua top article (untuk hitung %)
        $totalTopViews = $topArticles->sum('views');

        $topArticles = $topArticles->map(function ($a) use ($totalTopViews) {
            $percentage = $totalTopViews > 0
                ? round(($a['views'] / $totalTopViews) * 100)
                : 0;

            return [
                ...$a,
                'percentage' => $percentage,
            ];
        });
        return Inertia::render('Dashboard/Dashboard', [
            "stats" => $stats,
            "articles" => $articlesData,
            "categories" => $categoriesData,
            "topArticles" => $topArticles,
        ]);
    }
}
