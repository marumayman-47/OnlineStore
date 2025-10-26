@extends('layouts.app')

@section('title', $category->name)

@section('content')
<div class="container mx-auto p-6">
  <div class="bg-white rounded-xl shadow p-6">
    <div class="flex items-center gap-6 mb-6">
      @if($category->image)
        <img src="{{ asset('storage/' . $category->image) }}" class="w-32 h-32 object-cover rounded-xl">
      @endif
      <div>
        <h1 class="text-3xl font-semibold">{{ $category->name }}</h1>
        <p class="text-gray-600">{{ $category->description }}</p>
        <span class="inline-block mt-2 text-sm px-3 py-1 rounded-full {{ $category->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
          {{ $category->is_active ? 'Active' : 'Inactive' }}
        </span>
      </div>
    </div>

    <h2 class="text-xl font-semibold mb-4">Products in this category</h2>
    <div class="grid md:grid-cols-3 sm:grid-cols-2 gap-6">
      @forelse($products as $product)
        <div class="border rounded-lg shadow-sm p-4 bg-gray-50">
          <img src="{{ asset('storage/' . $product->image) }}" class="h-32 w-full object-cover rounded mb-2">
          <h3 class="text-lg font-medium">{{ $product->name }}</h3>
          <p class="text-gray-600 text-sm">{{ $product->price }} EGP</p>
        </div>
      @empty
        <p class="text-gray-500 col-span-full">No products found for this category.</p>
      @endforelse
    </div>

    <div class="mt-6">
      {{ $products->links('pagination::bootstrap-5') }}
    </div>
  </div>
</div>
@endsection
