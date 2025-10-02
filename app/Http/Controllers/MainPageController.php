<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class MainPageController extends Controller
{
    public function index()
    {
        $poparticle = Article::with('tags', 'user', 'categories')
            ->where('status', 'published')
            ->orderBy('views', 'desc')
            ->limit(5)
            ->get();

        $articles = Article::with('tags', 'user', 'categories', 'comments', 'likes')
            ->where('status', 'published')
            ->latest()
            ->limit(12)
            ->get();



        $articleData = $articles->map(function ($article) {
            $word_count = str_word_count($article->body);
            $words_per_minute = 200; // Or adjust as needed
            $read_time = ceil($word_count / $words_per_minute);
            return [
                'id' => $article->id,
                'title' => $article->title,
                'slug' => $article->slug,
                'excerpt' => $article->excerpt,
                'cover' => $article->cover,
                'created_at' => $article->created_at,
                'updated_at' => $article->updated_at,
                'user' => $article->user,
                'tags' => $article->tags,
                'categories' => $article->categories,
                'read_time' => $read_time,
                'comments' => $article->comments,
                'likes' => $article->likes,
            ];
        });


        if (Auth::check()) {
            return Inertia::render('Main/Home', [
                "popArticles" => $poparticle,
                // "pinnedArticle" => $pinnedArticle,
                "articles" => $articleData,
                "categories" => Category::all(),
            ]);
        } else {
            return Inertia::render('Main/Welcome', [
                "articles" => $poparticle,


            ]);
        }
    }
    public function show(Request $request, $id, $slug)
    {
        // Data Article
        $article = Article::with('tags', 'user', 'categories', 'likes')
            ->where('slug', $slug)
            ->firstOrFail();

        $user = auth()->user();
        $liked = $user ? $article->likes()->where('user_id', $user->id)->exists() : false;
        $likesCount = $article->likes()->count();

        // Data Comments
        $comments = $article->comments()
            ->with('user')
            ->latest()
            ->paginate(10);


        // Map comments to array
        $commentsData = $comments->map(function ($comment) {
            return [
                'id' => $comment->id,
                'content' => $comment->content,
                'user' => [
                    'id' => $comment->user->id ?? null,
                    'name' => $comment->user->name ?? 'Unknown',
                    'username' => $comment->user->username ?? 'Unknown',
                    'avatar' => $comment->user->avatar_url ?? null,
                ],
                'created_at' => $comment->created_at->format('Y-m-d H:i:s'),
            ];
        });
        $word_count = str_word_count($article->body);
        $words_per_minute = 200;
        $read_time =  ceil($word_count / $words_per_minute);
        // data articlesdata?
        $articledata = [
            "title" => $article->title,
            "body" => $article->body,
            "excerpt" => $article->excerpt,
            "user" => $article->user,
            "tags" => $article->tags,
            "likedByUser" => $liked,
            "likes_count" => $likesCount,
            "categories" => $article->categories,
            'created_at' => $article->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $article->updated_at->format('Y-m-d H:i:s'),
            'published_at' => $article->published_at->format('d M Y'),
            'read_time' => $read_time,
            "status" => $article->status,
            "slug" => $article->slug,
            "cover" => $article->cover,
            "cover_url" => $article->cover_url,
            "id" => $article->id,
            "views" => $article->views,
            "hits" => $article->hits,
        ];

        // dd($articledata);
        // Data: Recent Articles
        $recentArticle = Article::query()
            ->where([
                ['status', '=', 'published'],
                ['user_id', '=', $article->user_id],
            ])
            ->latest()
            ->take(3)
            ->get();

        // Data: hits dan views
        $article->increment('hits');
        $sessionKey = 'viewed_article_' . $article->id;
        if (!$request->session()->has($sessionKey)) {
            $article->increment('views');
            $request->session()->put($sessionKey, true);
        }

        // Return + Render
        return Inertia::render("Main/Read", [
            "article" => $articledata,
            "recent" => $recentArticle,
            "comments" => $commentsData,
            "commentsPagination" => [
                'current_page' => $comments->currentPage(),
                'last_page' => $comments->lastPage(),
                'per_page' => $comments->perPage(),
                'total' => $comments->total(),
            ],
            'initialLiked' => $liked,
            'initialCount' => $article->likes()->count(),
        ])->withViewData([
            'meta' => [
                'title' => $article->title,
                'description' => substr(strip_tags($article->body), 0, 150),
                'image' => $article->cover_url,
                'url' => url('/articles/' . $article->id),
            ]
        ]);
    }

    public function articles()
    {
        $categorized = Category::with(['articles' => function ($query) {
            $query->where('status', 'published')->latest();
        }, 'articles.user'])
            ->whereHas('articles', function ($query) {
                $query->where('status', 'published');
            })
            ->latest()
            ->paginate(5)
            ->withQueryString();
        $article = Article::with('tags', 'user', 'categories')
            ->where('status', 'published')
            ->orderBy('views', 'desc')
            ->limit(5)
            ->get();
        return Inertia::render('Main/Articles', [
            "categorized" => $categorized,
            "ArticlesPagination" => [
                "last_page" => $categorized->lastPage(),
                "current_page" => $categorized->currentPage(),
                "total" => $categorized->total(),
                "per_page" => $categorized->perPage(),
            ],
            "categories" => Category::all(),
            "popArticles" => $article,
        ]);
    }


    public function categories()
    {
        $categories = Category::all();
        // dd($categories);
        return Inertia::render('Main/Categories', [
            "categories" => $categories,
            "articles" => Article::all()
        ]);
    }

    public function category($slug)
    {
        $category = Category::with(['articles' => function ($q) {
            $q->latest()->paginate(18);
        }])->where('slug', $slug)->firstOrFail();

        $articles = $category->articles()->latest()->paginate(18)->withQueryString();
        $popCat = $category->articles()->limit(5)->orderBy('views', 'desc')->get();

        return Inertia::render('Main/Category', [
            'category' => $category,
            'popCat' => $popCat,
            'articlesPagination' => [
                'per_page' => $articles->perPage(),
                'current_page' => $articles->currentPage(),
                'last_page' => $articles->lastPage(),
                'total' => $articles->total(),
            ],
        ]);
    }


    public function search()
    {

        // cek mode (bisa dari .env, config, atau query string)
        $mode = config('app.data_mode', 'local'); // default local
        $search = request('search');

        if ($mode === 'api') {
            return Inertia::render('Main/Search', [
                'data_mode' => 'api',
                "filters" => [
                    "search" => $search
                ]
            ]);
        } else {


            $articles = Article::search($search)
                ->where('status', 'published')
                ->latest()
                ->paginate(18)
                ->withQueryString();


            return Inertia::render('Main/Search', [
                "articles" => $articles,
                "data_mode" => 'local',
                "ArticlesPagination" => [
                    "per_page" => $articles->perPage(),
                    "last_page" => $articles->lastPage(),
                    "current_page" => $articles->currentPage(),
                    "total" => $articles->total(),
                ],
                "filters" => [
                    "search" => $search
                ]
            ]);
        }
    }

    public function Typesense(Request $request)
    {
        $search = $request->input('q');
        $start = microtime(true);

        $articles = Article::search($search)
            ->paginate(5);

        $end = microtime(true);
        $timeTaken = round(($end - $start) * 1000, 2); // ms
        return response()->json([
            'articles' => $articles,
            'articlesTotal' => Article::count(),
            'time_taken_ms' => $timeTaken,
        ]);
    }
    public function profile($username)
    {
        $author = User::with('roles')
            ->where('username', $username)
            ->firstOrFail();

        $articles = Article::with('tags', 'user', 'categories')
            ->where('user_id', $author->id)
            ->where('status', 'published')
            ->latest()
            ->paginate(6)
            ->withQueryString();

        return Inertia::render('Main/Author', [
            'author' => $author,
            'articles' => $articles
        ]);
    }


    public function like(Article $article)
    {
        $user = auth()->user();

        $like = $article->likes()->where('user_id', $user->id)->first();

        if ($like) {
            // kalau udah like, hapus (unlike)
            $like->delete();
            $liked = false;
        } else {
            $article->likes()->create([
                'user_id' => $user->id
            ]);
            $liked = true;
        }

        return response()->json([
            'liked' => $liked,
            'likes_count' => $article->likes()->count(),
        ]);
    }
}
