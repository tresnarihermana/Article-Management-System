<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, $articleId)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        Comment::create([
            'article_id' => $articleId,
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        return redirect()->back();
    }

    public function destroy(string $id)
    {
        Comment::destroy($id);

        return back()->with("message", "Success Delete Article");
    }
}
