<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;


class CategoryController extends Controller
{
    /** List categories */
    public function index()
    {
        $categories = Category::latest()->paginate(6);
        return view('categories.index', compact('categories'));
    }

    /** Show create form */
    public function create()
    {
        return view('categories.create');
    }

    /** Store category */
    public function store(CategoryStoreRequest  $request)
    {
        // $validated = $request->validate([
        //     'name' => 'required|string|max:255|unique:categories,name',
        //     'description' => 'nullable|string',
        //     'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        //     'is_active' => 'boolean'
        // ]);

        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        Category::create($validated);

        return redirect()->route('categories.index')->with('success', 'Category added successfully!');
    }

    /** Show single category and its products */
    public function show(Category $category)
    {
        $products = $category->products()->latest()->paginate(6);
        return view('categories.show', compact('category', 'products'));
    }

    /** Edit form */
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    /** Update category */
    public function update(CategoryUpdateRequest  $request, Category $category)
    {
        // $validated = $request->validate([
        //     'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        //     'description' => 'nullable|string',
        //     'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        //     'is_active' => 'boolean'
        // ]);

        $validated = $request->validated();

        if ($request->hasFile('image')) {
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        // $category->update($validated);
        try {
        $category->update($validated);
        } 
        catch (\Exception $e) {
        \Log::error('Category update failed: '.$e->getMessage());
        return redirect()->back()->withInput()->with('error', 'Failed to update category. Please try again.');
        }

        return redirect()->route('categories.index')->with('success', 'Category updated successfully!');
    }

    /** Delete category */
    public function destroy(Category $category)
    {
        if ($category->image && Storage::disk('public')->exists($category->image)) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Category deleted successfully!');
    }
}
