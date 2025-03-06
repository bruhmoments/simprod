<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index()
    {
        return view('categories.index', ['categories' => Category::all()]);
    }

    public function create()
    {
        return view('categories.input');
    }

    // Pakai Prepared Statement
    public function store(Request $req)
    {
        $validated = $req->validate(['name' => 'required|unique:categories,name']);
        
        DB::insert('insert into categories (name) values (?)', [$validated['name']]);
        
        return redirect()->route('categories.index')->with('success', 'Category added');
    }

    public function edit(Category $category)
    {
        return view('categories.input', compact('category'));
    }

    public function update(Request $req, Category $category)
    {
        $validated = $req->validate(['name' => 'required|unique:categories,name']);

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'Category updated');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Category deleted');
    }

    // public function index()
    // {
    //     $kategori = Kategori::all();
    //     return view('admin.kategori.index', compact('kategori'));
    // }

    // public function create()
    // {
    //     return view('admin.kategori.input');
    // }

    // public function store(Request $request)
    // {
    //     $validated = $request->validate(['nama' => 'required']);
    //     Kategori::create($validated);
    //     return redirect()->route('kategori.index')->with('success', 'Kategori berhasil ditambahkan');
    // }

    // public function edit(Kategori $kategori)
    // {
    //     return view('admin.kategori.input', compact('kategori'));
    // }

    // public function update(Request $request, Kategori $kategori)
    // {
    //     $validated = $request->validate(['nama' => 'required']);
    //     $kategori->update($validated);
    //     return redirect()->route('kategori.index')->with('success', 'Kategori berhasil diupdate');
    // }
}
