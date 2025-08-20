<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Inertia\Inertia;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ManageArticleController extends Controller
{

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $status = $request->input('status');
        $articles = Article::with('user', 'tags', 'categories')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%$search%")
                        ->orWhere('slug', 'like', "%$search%");
                });
            })->when($status, function ($query, $status) {
                $query->where(function ($q) use ($status) {
                    $q->where('status', '=', $status);
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
                    'cover' => $article->cover,
                    'rejected_message' => $article->rejected_message,
                ];
            });
        return Inertia::render('Articles/Manage/Index', [
            'articles' => $articles,
            'search' => $search,
            'filters' => $request->only(['per_page', 'status', 'search'])


        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();
        // dd($categories);
        return Inertia::render("Articles/Create", [
            'categories' => $categories,
            'tags' => $tags
        ]);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'excerpt' => 'nullable|string',
            'category_id' => 'nullable|int',
            'category_ids.*' => 'exists:categories,id',
            'cover' => 'nullable|image|max:10240',
            'tag_ids' => 'nullable|array',
            'tags_ids.*' => 'exists:tags_id',
            'status' => 'nullable|in:draft,pending',
        ]);

        $article = Article::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'body' => $validated['body'],
            'excerpt' => $validated['excerpt'] ?? Str::limit(strip_tags($validated['body']), 150),
            'status' => $validated['status'] ?? 'draft',
        ]);
        if (!empty($validated['category_id'])) {
            $article->categories()->attach($validated['category_id']);
        }

        if (!empty($validated['tag_ids'])) {
            $article->tags()->attach($validated['tag_ids']);
        }
        if ($request->hasFile('cover')) {
            $link = Storage::disk('public')->putFile('cover', $request->file('cover'));
            $article->cover = $link;
            $article->save();
        }
        return to_route("articles.index")->with("message", "Success Create Article");
    }

    public function show($slug)
    {
        $article = Article::with('user', 'tags', 'categories')
            ->where('slug', $slug)
            ->firstOrFail();

        return Inertia::render("Articles/Show", [
            "article" => $article,
        ]);
    }


    public function edit(string $id)
    {
        $article = Article::with('user', 'categories', 'tags')->findOrFail($id);
        return Inertia::render("Articles/Edit", [
            "article" => $article,
            "categories" => Category::all(),
            "tags" => Tag::all(),

        ]);
    }

    public function update(Request $request, string $id)
    {
        // dd($request->all());
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required',
            'excerpt' => 'nullable|string',
            'category_id' => 'nullable|int',
            'cover' => 'nullable|max:10240',
            // 'category_ids.*' => 'exists:categories,id',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:tags,id',
            'status' => 'nullable|in:draft,pending',
        ]);

        $article = Article::findOrFail($id);
        $article->update($request->only(['title', 'body', 'excerpt', 'cover', 'status']));

        // 'excerpt' => $validated['excerpt'] ?? Str::limit(strip_tags($validated['body']), 150),
        // 'status' => $validated['status'] ?? $article->status,




        if (!empty($validated['category_id'])) {
            $article->categories()->sync($validated['category_id']);
        }

        if (!empty($validated['tag_ids'])) {
            $article->tags()->sync($validated['tag_ids']);
        }
        if (!empty($validated['cover'])) {
            $article->cover = $validated['cover'];
        }
        $article->save();
        return to_route("articles.index")->with("message", "Success Edit Articles");
    }

    public function destroy(string $id)
    {
        Article::destroy($id);

        return to_route("approve.index")->with("message", "Success Delete Article");
    }


    public function uploadCover(Request $request)
    {
        $request->validate([
            'cover' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        $path = Storage::disk('public')->putFile('cover', $request->file('cover'));

        return response()->json([
            'path' => $path
        ]);
    }
}
