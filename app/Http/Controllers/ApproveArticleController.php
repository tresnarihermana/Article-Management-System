<?php

namespace App\Http\Controllers;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class ApproveArticleController extends Controller
{
    public function approve(Article $article): RedirectResponse
    {
    //  dd($article->getAttribute('status'));
        $article->update(['status' => 'published', 'published_at' => now()]);
        return back()->with('success', 'Article approved!');
    }

    public function reject(Request $request, string $id): RedirectResponse
    {
        // dd($request->all());
        $request->validate([
            'rejected_message' => 'required|string|max:255',
        ]);
        $article = Article::findOrFail($id);
        $article->update(['published_at' => null, 'status' => 'rejected'],$request->only(['rejected_message']));
        return back()->with('success', 'Article rejected!');
    }
}
