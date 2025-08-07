<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MainPageController extends Controller
{
    public function index()
    {
        $article = Article::with('tags', 'user', 'categories')
            ->where('status', 'published')
            ->limit(3)
            ->latest()
            ->get();
        return Inertia::render('Welcome',[
            "articles" => $article,
            
        ]);
    }
}
