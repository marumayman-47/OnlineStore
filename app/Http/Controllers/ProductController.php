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
    // List products with search, filters, sort, and pagination
    public function index(Request $request)
    {
        $search = $request->query('search');
        $categoryId = $request->query('category_id');
        $minPrice = $request->query('min_price');
        $maxPrice = $request->query('max_price');
        $sort = $request->query('sort');

        $products = Product::with('comments')->get();
        $query = Product::with('category');

        $query->when($search, fn($q) =>
            $q->where('name', 'like', "%{$search}%")
        )->when($request->category_id, fn($q) =>
            $q->where('category_id', $request->category_id)
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
        $categories = \App\Models\Category::where('is_active', true)->orderBy('name')->get();

        // If AJAX (for live search), return partial HTML only
        if ($request->ajax()) {
            return view('products.partials.list', compact('products'))->render();
        }

        return view('products.index', compact('products', 'categories'));
        // return view('products.index', compact('products'));
    }

    // Show create form
    public function create()
    {
        $categories = \App\Models\Category::where('is_active', true)->orderBy('name')->get();
        return view('products.create', compact('categories'));
    }

    // Store new product
    public function store(ProductStoreRequest  $request)
    {
        // $validated = $request->validate([
        //     'name'           => 'required|string|max:255',
        //     'description'    => 'nullable|string',
        //     'price'          => 'required|numeric|min:0',
        //     'category_id'    => 'required|exists:categories,id',
        //     'stock_quantity' => 'nullable|integer|min:0',
        //     'is_active'      => 'sometimes|boolean',
        //     'image'          => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048|dimensions:max_width=2000,max_height=2000',
        // ]);

        // $request
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();
        $product = Product::create($validated);


        // defaults
        $validated['stock_quantity'] = $validated['stock_quantity'] ?? 0;
        $validated['is_active'] = $request->boolean('is_active', true);

        // handle image upload with unique name
        if ($request->hasFile('image')) 
        {
            $file = $request->file('image');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->storeAs('products', $filename, 'public');
            $validated['image'] = 'products/'.$filename; // save folder + filename in DB
        }

        // Create product
        $product = Product::create($validated);

        return redirect()->route('products.index')->with('success', ' Product created successfully.');
    }

    // Show single product
    public function show(Product $product)
    {
        $product->load('category');

        // If ?edit={id} in the URL, set it in the session
        if (request()->has('edit')) {
            session(['edit_comment_id' => request('edit')]);
        } else {
            session()->forget('edit_comment_id');
        }
        
        return view('products.show', compact('product'));
    }

    // Show edit form
    public function edit(Product $product)
    {
        $categories = \App\Models\Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    // Update product
    public function update(ProductUpdateRequest  $request, Product $product)
    {
        // $validated = $request->validate([
        //     'name'           => 'required|string|max:255',
        //     'description'    => 'nullable|string',
        //     'price'          => 'required|numeric|min:0',
        //     'category_id'    => 'required|exists:categories,id',
        //     'stock_quantity' => 'nullable|integer|min:0',
        //     'is_active'      => 'sometimes|boolean',
        //     'image'          => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048|dimensions:max_width=2000,max_height=2000',
        // ]);

        $validated = $request->validated();

        // Set defaults
        $validated['stock_quantity'] = $validated['stock_quantity'] ?? $product->stock_quantity ?? 0;
        $validated['is_active'] = $request->boolean('is_active', $product->is_active);
        // $validated['category_id'] = $request->category_id;

        // Handle image upload and safely replace old image
        if ($request->hasFile('image')) 
        {
            // Delete old image if it exists
            if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

            // Store new image with a unique name
            $file = $request->file('image');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('products', $filename, 'public');
            $validated['image'] = 'products/'.$filename; // store folder + filename in DB
        }

        
        // Update model
        //$product->update($validated);

        try {
        $product->update($validated);
        } 
        catch (\Exception $e) {
        
        \Log::error('Product update failed: '.$e->getMessage());
        return redirect()->back()->withInput()->with('error', 'Failed to update product. Please try again.');
        }

        return redirect()
            ->route('products.show', $product)
            ->with('success', 'Product updated successfully.');
    }

    // Delete product and its image
    public function destroy(Product $product)
    {
        try {
            // Delete image if it exists in storage
            if (!empty($product->image) && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            // Delete product record
            $product->delete();

            return redirect()
                ->route('products.index')
                ->with('success', 'Product deleted successfully.');
        } 
        catch (\Exception $e) {
            // handle unexpected errors gracefully
            return redirect()
                ->route('products.index')
                ->with('error', ' Failed to delete product: ' . $e->getMessage());
        }
    }
}
