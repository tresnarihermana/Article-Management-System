<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TagController extends Controller
{
    public function index(Request $request)
    {

        $tags = Tag::with('articles')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($tag) {
                return [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'created_at' => $tag->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $tag->updated_at->format('Y-m-d H:i:s'),
                    'slug' => $tag->slug,
                    'articles' => $tag->articles->count()
                ];
            });
        return Inertia::render('Tags/Index', [
            'tags' => $tags,
        ]);
    }

    public function create()
    {
        return Inertia::render('Tags/Create', []);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:' . Tag::class,
        ]);
        Tag::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);
        return to_route('tags.index')->with('message', 'Tambah Tag Berhasil');
    }

    public function edit($id)
    {
        // Show a form to edit an existing tag
        $tag = Tag::findOrFail($id);
        return Inertia::render('Tags/Edit', [
            'tag' => $tag,
        ]);
    }

    public function update(Request $request, $id)
    {
        // Validate and update an existing tag
        $tag = Tag::findOrFail($id);
        $request->validate([
            'name' => 'required|string|unique:' . Tag::class,
        ]);
        $tag->update($request->only(['name']));
        $tag->slug = Str::slug($request->name);
        $tag->save();
        return to_route('tags.index')->with('message', 'tag berhasil di edit');
    }

    public function destroy($id)
    {
        // Delete an existing tag
        Tag::destroy($id);
        return to_route('tags.index')->with('message', 'Tag berhasil dihapus');
    }
    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        // dd($ids);
        if (!empty($ids)) {
            Tag::whereIn('id', $ids)->delete();
        }

        return redirect()->back(303)->with('success', 'Selected users deleted successfully');
    }
}
