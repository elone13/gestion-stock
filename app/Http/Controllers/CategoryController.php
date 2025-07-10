<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->get();
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
            'color' => 'required|string|max:7'
        ]);

        Category::create($request->all());
        return redirect()->route('categories.index')->with('success', 'Catégorie créée avec succès !');
    }

    public function show(Category $category)
    {
        $products = $category->products;
        return view('categories.show', compact('category', 'products'));
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'color' => 'required|string|max:7'
        ]);

        $category->update($request->all());
        return redirect()->route('categories.index')->with('success', 'Catégorie modifiée avec succès !');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return redirect()->route('categories.index')->with('error', 'Impossible de supprimer une catégorie qui contient des produits.');
        }

        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Catégorie supprimée avec succès !');
    }
}
