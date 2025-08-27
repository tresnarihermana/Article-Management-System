<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MainPageController extends Controller
{
    public function index()
    {
        $categorized = Category::with(['articles' => function ($query) {
            $query->where('status', 'published')->latest();
        }, 'articles.user'])
            ->whereHas('articles', function ($query) {
                $query->where('status', 'published');
            })
            ->latest()
            ->limit(4)
            ->get();
        $poparticle = Article::with('tags', 'user', 'categories')
            ->where('status', 'published')
            ->orderBy('views', 'desc')
            ->limit(5)
            ->get();

        $pinnedArticle =  Article::with('tags', 'user', 'categories')
            ->where('status', 'published')
            ->where('is_pinned', true)
            ->limit(7)
            ->get();

        if (Auth::check()) {
            return Inertia::render('Home', [
                "popArticles" => $poparticle,
                "pinnedArticle" => $pinnedArticle,
                "categorized" => $categorized,
                "categories" => Category::all(),



            ]);
        } else {
            return Inertia::render('Welcome', [
                "articles" => $poparticle,
                "categorized" => $categorized


            ]);
        }
    }
    public function show(Request $request, $slug)
    {
        $article = Article::with('tags', 'user', 'categories', 'likes')
            ->where('slug', $slug)
            ->firstOrFail();

        $user = auth()->user();
        $liked = $user ? $article->likes()->where('user_id', $user->id)->exists() : false;
        $likesCount = $article->likes()->count();


        $comments = $article->comments()
            ->with('user')
            ->latest()
            ->paginate(10);



        $commentsData = $comments->map(function ($comment) {
            return [
                'id' => $comment->id,
                'content' => $comment->content,
                'user' => [
                    'id' => $comment->user->id ?? null,
                    'name' => $comment->user->name ?? 'Unknown',
                    'username' => $comment->user->username ?? 'Unknown',
                    'avatar' => $comment->user->avatar ?? null,
                ],
                'created_at' => $comment->created_at->format('Y-m-d H:i:s'),
            ];
        });

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
            "status" => $article->status,
            "slug" => $article->slug,
            "cover" => $article->cover,
            "id" => $article->id,
            "views" => $article->views,
            "hits" => $article->hits,
        ];

        $recentArticle = Article::query()
            ->where([
                ['status', '=', 'published'],
                ['user_id', '=', $article->user_id],
            ])
            ->latest()
            ->take(3)
            ->get();

        // hits dan views
        $article->increment('hits');
        $sessionKey = 'viewed_article_' . $article->id;
        if (!$request->session()->has($sessionKey)) {
            $article->increment('views');
            $request->session()->put($sessionKey, true);
        }

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
        $search = request('search');



        $articles = Article::query()
            ->when($search, function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('slug', 'like', '%' . $search . '%');
            })
            ->latest()
            ->paginate(18)
            ->withQueryString();

        return Inertia::render('Main/Search', [
            "articles" => $articles,
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
