@extends('layouts.app')

@section('title', 'Edit Category')

@section('content')
<div class="container mx-auto p-6">
  <div class="bg-white p-6 rounded-xl shadow">
    <h2 class="text-2xl font-semibold mb-4">Edit Category</h2>

    <form action="{{ route('categories.update', $category) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
      @csrf
      @method('PUT')

      <div>
        <label class="block font-medium mb-1">Name</label>
        <input type="text" name="name" value="{{ old('name', $category->name) }}" class="form-control w-full border rounded p-2" required>
        @error('name') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block font-medium mb-1">Description</label>
        <textarea name="description" rows="3" class="form-control w-full border rounded p-2">{{ old('description', $category->description) }}</textarea>
      </div>

      <div>
        <label class="block font-medium mb-1">Image</label>
        <input type="file" name="image" class="form-control w-full border rounded p-2">
        @if($category->image)
          <img src="{{ asset('storage/' . $category->image) }}" class="h-24 w-24 object-cover mt-2 rounded" alt="">
        @endif
      </div>

      <div>
        <label class="inline-flex items-center">
          <input type="checkbox" name="is_active" value="1" {{ $category->is_active ? 'checked' : '' }} class="mr-2"> Active
        </label>
      </div>

      <div class="flex justify-end">
        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2 rounded-lg shadow">
          Update
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
