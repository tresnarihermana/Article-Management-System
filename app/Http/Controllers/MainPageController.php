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
        $categories = Category::with(['articles' => function ($query) {
            $query->where('status', 'published')->latest();
        }])
            ->whereHas('articles', function ($query) {
                $query->where('status', 'published');
            })
            ->latest()
            ->get();

        $article = Article::with('tags', 'user', 'categories')
            ->where('status', 'published')
            ->latest()
            ->limit(3)
            ->get();
        if (Auth::check()) {
            return Inertia::render('Home', [
                "articles" => $article,
                "categories" => $categories


            ]);
        } else {
            return Inertia::render('Welcome', [
                "articles" => $article,
                "categories" => $categories


            ]);
        }
    }
    public function show($slug)
    {
        $article = Article::with('tags', 'user', 'categories')
            ->where('slug', $slug)
            ->firstOrFail();
        $articledata =  [
            "title" => $article->title,
            "body" => $article->body,
            "excerpt" => $article->excerpt,
            "user" => $article->user,
            "tags" => $article->tags,
            "categories" => $article->categories,
            'created_at' => $article->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $article->updated_at->format('Y-m-d H:i:s'),
            'published_at' => $article->published_at->format('d M Y'),
            "status" => $article->status,
            "slug" => $article->slug,
            "cover" => $article->cover,
            "id" => $article->id,
        ];
        $recentArticle = Article::query()
            ->where([
                ['status', '=', 'published'],
                ['user_id', '=', $article->user_id],
            ])
            ->latest()
            ->take(3)
            ->get();
        return Inertia::render("Main/Read", [
            "article" => $articledata,
            "recent" => $recentArticle,

        ]);
    }
    public function articles()
    {
        $categories = Category::with(['articles' => function ($query) {
            $query->where('status', 'published')->latest();
        }])
            ->whereHas('articles', function ($query) {
                $query->where('status', 'published');
            })
            ->latest()
            ->get();
        return Inertia::render('Main/Articles', [
            "categories" => $categories,
            "articles" => Article::all()
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

        $category = Category::with('articles')
            ->where('slug', $slug)
            ->latest()
            ->get();
        return Inertia::render('Main/Category', [
            "category" => $category,


        ]);
    }

    public function search()
    {
        $search = request('search');



        $articles = Article::query()
            ->when($search, function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('body', 'like', '%' . $search . '%');
            })
            ->latest()
            ->get();

        return Inertia::render('Main/Search', [
            "articles" => $articles,
            "filters" => [
                "search" => $search
            ]
        ]);
    }
public function author($username)
{
    $author = Article::with('user')
        ->whereHas('user', function ($query) use ($username) {
            $query->where('username', $username);
        })
        ->get();

    return Inertia::render('Main/Author', [
        'author' => $author
    ]);
}

}
