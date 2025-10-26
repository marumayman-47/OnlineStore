@extends('layouts.app')
@section('title', 'Add Product')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-6 rounded-2xl shadow-md">
  <h1 class="text-3xl font-bold mb-5 text-slate-800"> Add Product</h1>

  <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
    @csrf

    {{-- Name --}}
    <div>
      <label class="block text-sm font-semibold mb-1 text-gray-700">Name</label>
      <input type="text" name="name" value="{{ old('name') }}"
        class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400">
      @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Category --}}
    <div>
      <label class="block text-sm font-semibold mb-1 text-gray-700">Category</label>
      <select name="category_id"
        class="w-full border border-gray-300 rounded-lg p-2.5 bg-white focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400">
        <option value="">-- Select Category --</option>
        @foreach ($categories as $category)
          <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
            {{ $category->name }}
          </option>
        @endforeach
      </select>
      @error('category_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Description --}}
    <div>
      <label class="block text-sm font-semibold mb-1 text-gray-700">Description</label>
      <textarea name="description" rows="3"
        class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400">{{ old('description') }}</textarea>
    </div>

    {{-- Price + Stock --}}
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-semibold mb-1 text-gray-700">Price ($)</label>
        <input type="number" step="0.01" name="price" value="{{ old('price') }}"
          class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400">
        @error('price') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-semibold mb-1 text-gray-700">Stock Quantity</label>
        <input type="number" name="stock_quantity" value="{{ old('stock_quantity', 0) }}"
          class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400">
      </div>
    </div>

    {{-- Active --}}
    <div class="flex items-center gap-2">
      <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
      <label class="text-sm text-gray-700">Active</label>
    </div>

    {{-- Image --}}
    <div>
      <label class="block text-sm font-semibold mb-1 text-gray-700">Product Image</label>
      <input type="file" name="image" id="imageInput" accept="image/*"
        class="w-full border border-gray-300 rounded-lg p-2.5 bg-white">
      <div id="preview" class="mt-3 hidden">
        <img id="previewImg" class="w-40 h-40 object-cover rounded-lg shadow">
      </div>
    </div>

    {{-- Actions --}}
    <div class="flex justify-between items-center pt-4">
      <a href="{{ route('products.index') }}" class="text-gray-600 hover:underline">← Cancel</a>
      <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg shadow">
        Create Product
      </button>
    </div>
  </form>
</div>

<script>
  document.getElementById('imageInput').addEventListener('change', e => {
    const file = e.target.files[0];
    if (!file) return;
    const img = document.getElementById('previewImg');
    img.src = URL.createObjectURL(file);
    document.getElementById('preview').classList.remove('hidden');
  });
</script>
@endsection
