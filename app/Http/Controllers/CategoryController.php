<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $categories = Category::with('articles')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                        ->orWhere('slug', 'like', "%$search%");
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString()
            ->through(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'created_at' => $category->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $category->updated_at->format('Y-m-d H:i:s'),
                    'description' => $category->description,
                    'articles' => $category->articles,
                    'slug' => $category->slug,
                ];
            });
        return Inertia::render('Categories/Index', [
            'categories' => $categories,
            'search' => $search,
            'filters' => $request->only('input'),

        ]);
    }

    public function create()
    {
        // Show the form for creating a new resource.
        return Inertia::render('Categories/Create',[
            
        ]);
    }

    public function store(Request $request)
    {
        // Store a newly created resource in storage.
        $request->validate([
            'name' => 'required|string|unique:'.Category::class,
            'description' => 'required|string|max:255'
        ]);
        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);
        return to_route('categories.index')->with('message', 'category berhasil dibuat');
    }

    public function show($id)
    {
        // Display the specified resource.
    }

    public function edit($id)
    {
        $category = Category::findorFail($id);
        return Inertia::render('Categories/Edit',[
            'category' => $category
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|unique:'. Category::class,
            'description' => 'required|string|max:255'
        ]);
        $category = Category::findOrFail($id);
        $category->update($request->only(['name', 'description']));
        $category->slug = Str::slug($request->name);
        $category->save();
        return to_route('categories.index')->with('message', 'Category berhasil di Ubah');
      
    }

    public function destroy($id)
    {
        // Remove the specified resource from storage.
        Category::destroy($id);
        return to_route('categories.index')->with('message', 'category berhasil di hapus');
    }
}
