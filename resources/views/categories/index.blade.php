@extends('layouts.app')

@section('title', 'Categories')

@section('content')
<div class="container mx-auto p-6">
  <div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Categories</h1>
    <a href="{{ route('categories.create') }}" class="btn btn-primary bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
      + Add Category
    </a>
  </div>

  @if(session('success'))
    <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
  @endif

  <div class="grid md:grid-cols-3 sm:grid-cols-2 gap-6">
    @forelse ($categories as $category)
      <div class="border rounded-xl shadow hover:shadow-lg transition bg-white">
        @if($category->image)
          <img src="{{ asset('storage/' . $category->image) }}" class="w-full h-40 object-cover rounded-t-xl" alt="{{ $category->name }}">
        @else
          <div class="w-full h-40 bg-gray-200 flex items-center justify-center text-gray-500">No Image</div>
        @endif
        <div class="p-4">
          <h3 class="text-lg font-semibold">{{ $category->name }}</h3>
          <p class="text-sm text-gray-600 line-clamp-2">{{ $category->description }}</p>
          <div class="flex justify-between items-center mt-4">
            <span class="text-xs px-2 py-1 rounded-full {{ $category->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
              {{ $category->is_active ? 'Active' : 'Inactive' }}
            </span>
            <div class="flex gap-2">
              <a href="{{ route('categories.edit', $category) }}" class="text-blue-600 hover:underline">Edit</a>
              <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Delete this category?')">
                @csrf @method('DELETE')
                <button type="submit" class="text-red-600 hover:underline">Delete</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    @empty
      <p class="text-gray-500 col-span-full">No categories found.</p>
    @endforelse
  </div>

  <div class="mt-6">
    {{ $categories->links('pagination::bootstrap-5') }}
  </div>
</div>
@endsection
