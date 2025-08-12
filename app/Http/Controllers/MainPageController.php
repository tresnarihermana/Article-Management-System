<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MainPageController extends Controller
{
    public function index()
    {
        $article = Article::with('tags', 'user', 'categories')
            ->where('status', 'published')
            ->latest()
            ->get();
        return Inertia::render('Welcome', [
            "articles" => $article,


        ]);
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
            'published_at' => $article->published_at->format('Y-m-d H:i:s'),
            "status" => $article->status,
            "slug" => $article->slug,
            "cover" => $article->cover,
            "id" => $article->id,
        ];
        return Inertia::render("Main/Read", [
            "article" => $articledata,

        ]);
    }
    public function articles()
    {
        $article = Article::with('tags', 'user', 'categories')
            ->where('status', 'published')
            ->latest()
            ->get();
        return Inertia::render('Main/Articles', [
            "articles" => $article,
            "categories" => Category::all()
        ]);
    }
}
