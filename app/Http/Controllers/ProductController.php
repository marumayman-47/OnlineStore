<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;

class ProductController extends Controller
{
    // Public: List products with search, filters, sort, and pagination
    public function index(Request $request)
    {
        $search = $request->query('search');
        $categoryId = $request->query('category_id');
        $minPrice = $request->query('min_price');
        $maxPrice = $request->query('max_price');
        $sort = $request->query('sort');
        $showTrashed = $request->query('show_trashed');

        $query = Product::with(['category', 'comments']);

        // Show trashed items if requested (admin/manager only)
        if ($showTrashed && auth()->check() && auth()->user()->hasAnyRole(['admin', 'manager'])) {
            $query->onlyTrashed();
        }

        // Apply filters
        $query->when($search, fn($q) =>
            $q->where('name', 'like', "%{$search}%")
        )->when($categoryId, fn($q) =>
            $q->where('category_id', $categoryId)
        )->when($minPrice, fn($q) =>
            $q->where('price', '>=', $minPrice)
        )->when($maxPrice, fn($q) =>
            $q->where('price', '<=', $maxPrice)
        )->when($sort === 'price_asc', fn($q) =>
            $q->orderBy('price', 'asc')
        )->when($sort === 'price_desc', fn($q) =>
            $q->orderBy('price', 'desc')
        )->when($sort === 'oldest', fn($q) =>
            $q->oldest()
        )->when($sort === 'newest', fn($q) =>
            $q->latest()
        );

        $products = $query->paginate(8)->withQueryString();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        // AJAX request - return partial HTML only
        if ($request->ajax()) {
            return view('products.partials.list', compact('products'))->render();
        }

        return view('products.index', compact('products', 'categories'));
    }

    // Protected: Show create form
    public function create()
    {
        // This route is already protected by auth middleware
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('products.create', compact('categories'));
    }

    // Protected: Store new product
    public function store(ProductStoreRequest $request)
    {
        $validated = $request->validated();
        
        // Associate with authenticated user
        $validated['user_id'] = auth()->id();
        
        // Defaults
        $validated['stock_quantity'] = $validated['stock_quantity'] ?? 0;
        $validated['is_active'] = $request->boolean('is_active', true);

        // Handle image upload with unique name
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('products', $filename, 'public');
            $validated['image'] = 'products/' . $filename;
        }

        $product = Product::create($validated);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    // Public: Show single product
    public function show(Product $product)
    {
        $product->load('category', 'comments');

        // Handle edit mode for comments
        if (request()->has('edit')) {
            session(['edit_comment_id' => request('edit')]);
        } else {
            session()->forget('edit_comment_id');
        }
        
        return view('products.show', compact('product'));
    }

    // Protected: Show edit form
    public function edit(Product $product)
    {
        // Check if user owns this product
        if (auth()->id() !== $product->user_id && !auth()->user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    // Protected: Update product
    public function update(ProductUpdateRequest $request, Product $product)
    {
        // Authorization check
        if (auth()->id() !== $product->user_id && !auth()->user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validated();

        $validated['stock_quantity'] = $validated['stock_quantity'] ?? $product->stock_quantity ?? 0;
        $validated['is_active'] = $request->boolean('is_active', $product->is_active);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $file = $request->file('image');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('products', $filename, 'public');
            $validated['image'] = 'products/' . $filename;
        }

        try {
            $product->update($validated);
        } catch (\Exception $e) {
            \Log::error('Product update failed: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update product. Please try again.');
        }

        return redirect()
            ->route('products.show', $product)
            ->with('success', 'Product updated successfully.');
    }

    // Protected: Delete product
    public function destroy(Product $product)
    {
        // Authorization check
        if (auth()->id() !== $product->user_id && !auth()->user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Delete image if exists
            if (!empty($product->image) && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $product->delete();

            return redirect()
                ->route('products.index')
                ->with('success', 'Product deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('products.index')
                ->with('error', 'Failed to delete product: ' . $e->getMessage());
        }
    }

    // Protected: Restore soft-deleted product
    public function restore($id)
    {
        // Only admin and managers can restore
        if (!auth()->user()->hasAnyRole(['admin', 'manager'])) {
            abort(403, 'Unauthorized action.');
        }

        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore();

        return redirect()
            ->route('products.index')
            ->with('success', 'Product restored successfully.');
    }

    // Protected: Permanently delete product
    public function forceDelete($id)
    {
        // Only admins can permanently delete
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $product = Product::onlyTrashed()->findOrFail($id);
        
        // Delete image if exists
        if (!empty($product->image) && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->forceDelete();

        return redirect()
            ->route('products.index', ['show_trashed' => 1])
            ->with('success', 'Product permanently deleted.');
    }
}