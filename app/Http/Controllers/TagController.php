<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;

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
        return Inertia::render('Dashboard/Tags/TagsDataTables', [
            'tags' => $tags,
        ]);
    }

    public function create($id)
    {
        $category = Category::findOrFail($id);
        return Inertia::render('Dashboard/Tags/TagsCreate', [
            'category' => $category
        ]);
    }

public function store(Request $request)
{  
    // dd($request->all());
    $request->validate([
        'name' => 'required|string|unique:tags,name',
        'category_id' => 'required',       
    ]);

    $tag = Tag::create([
        'name' => $request->name,
        'slug' => Str::slug($request->name),
    ]);

    $tag->categories()->sync($request->category_id); 

   return back()->with('message', 'Tambah Tag Berhasil');
}



    public function edit($id)
    {
        // Show a form to edit an existing tag
        $tag = Tag::findOrFail($id);
        return Inertia::render('Dashboard/Tags/TagsEdit', [
            'tag' => $tag,
        ]);
    }

public function update(Request $request, $id)
{
    $tag = Tag::findOrFail($id);

    $request->validate([
        'name' => 'required|string|unique:tags,name,' . $id,
        'category_id' => 'required',
        'category_id.*' => 'exists:categories,id',
    ]);

    $tag->update([
        'name' => $request->name,
        'slug' => Str::slug($request->name),
    ]);
    $tag->categories()->sync($request->category_id);

    return back()->with('message', 'Tag berhasil di edit');
}


    public function destroy($id)
    {
        // Delete an existing tag
        Tag::destroy($id);
        return back()->with('message', 'Tag berhasil dihapus');
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
