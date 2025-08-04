<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Inertia\Inertia;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class ArticleController extends Controller
{

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $tag = $request->input('role');
        $articles = Article::with('user', 'tags', 'categories')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                        ->orWhere('username', 'like', "%$search%");
                });
            })->when($tag, function ($query, $tag) {
                $query->whereHas('tags', function ($q) use ($tag) {
                    $q->where('name', '=', $tag);
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString()
             ->through(function ($article) {
        return [
            'id' => $article->id,
            'title' => $article->title,
            'user' => $article->user,
            'tags' => $article->tags,
            'categories' => $article->categories,
            'created_at' => $article->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $article->updated_at->format('Y-m-d H:i:s'),
            'status' => $article->status,
            'slug' => $article->slug,
        ];
             });
        return Inertia::render('Articles/Index', [
            'articles' => $articles,
            'search' => $search,
            'filters' => $request->only('input', 'tag'),
            
        ]);
    }

       /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render("Users/Create", [
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'excerpt' => 'nullable|string',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:tags,id',
            'status' => 'nullable|in:draft,published,pending',
        ]);

        $article = Article::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'body' => $validated['body'],
            'excerpt' => $validated['excerpt'] ?? Str::limit(strip_tags($validated['body']), 150),
            'status' => $validated['status'] ?? 'draft',
        ]);
        if (!empty($validated['category_ids'])) {
            $article->categories()->attach($validated['category_ids']);
        }

        if (!empty($validated['tag_ids'])) {
            $article->tags()->attach($validated['tag_ids']);
        }

        return response()->json(['message' => 'Artikel berhasil dibuat.', 'data' => $article]);
    }

    // 🔎 Tampilkan detail artikel
    public function show($slug)
    {
        $article = Article::with('user', 'tags', 'categories', 'comments.replies')
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json($article);
    }


    public function edit(string $id)
    {
        $article = Article::findOrFail($id);
        return Inertia::render("Articles/Edit", [
            "article" => $article,
            "categories" => Category::all(),
            "tags" => Tag::all(),
        ]);
    }

    public function update(Request $request, Article $article)
    {
        $this->authorize('update', $article);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'excerpt' => 'nullable|string',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:tags,id',
            'status' => 'nullable|in:draft,published,pending',
        ]);

        $article->update([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'body' => $validated['body'],
            'excerpt' => $validated['excerpt'] ?? Str::limit(strip_tags($validated['body']), 150),
            'status' => $validated['status'] ?? $article->status,
        ]);

        if (!empty($validated['category_ids'])) {
            $article->categories()->sync($validated['category_ids']);
        }

        if (!empty($validated['tag_ids'])) {
            $article->tags()->sync($validated['tag_ids']);
        }

        return response()->json(['message' => 'Artikel berhasil diperbarui.']);
    }

    public function destroy(Article $article)
    {
        $this->authorize('delete', $article); // opsional: periksa izin

        $article->delete();

        return response()->json(['message' => 'Artikel dihapus.']);
    }
}
