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
    public function index(Request $request)
    {
        $showTrashed = $request->query('show_trashed');
        
        $query = Category::latest();

        // Show trashed items if requested (admin/manager only)
        if ($showTrashed && auth()->check() && auth()->user()->hasAnyRole(['admin', 'manager'])) {
            $query->onlyTrashed();
        }

        $categories = $query->paginate(6);
        return view('categories.index', compact('categories', 'showTrashed'));
    }

    /** Show create form */
    public function create()
    {
        return view('categories.create');
    }

    /** Store category */
    public function store(CategoryStoreRequest $request)
    {
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
    public function update(CategoryUpdateRequest $request, Category $category)
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        try {
            $category->update($validated);
        } catch (\Exception $e) {
            \Log::error('Category update failed: '.$e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to update category. Please try again.');
        }

        return redirect()->route('categories.index')->with('success', 'Category updated successfully!');
    }

    /** Soft delete category */
    public function destroy(Category $category)
    {
        $category->delete(); // Soft delete
        return redirect()->route('categories.index')->with('success', 'Category deleted successfully!');
    }

    /** Restore soft-deleted category */
    public function restore($id)
    {
        // Only admin and managers can restore
        if (!auth()->user()->hasAnyRole(['admin', 'manager'])) {
            abort(403, 'Unauthorized action.');
        }

        $category = Category::onlyTrashed()->findOrFail($id);
        $category->restore();

        return redirect()
            ->route('categories.index')
            ->with('success', 'Category restored successfully!');
    }

    /** Permanently delete category */
    public function forceDelete($id)
    {
        // Only admins can permanently delete
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $category = Category::onlyTrashed()->findOrFail($id);
        
        if ($category->image && Storage::disk('public')->exists($category->image)) {
            Storage::disk('public')->delete($category->image);
        }

        $category->forceDelete();

        return redirect()
            ->route('categories.index', ['show_trashed' => 1])
            ->with('success', 'Category permanently deleted!');
    }
}