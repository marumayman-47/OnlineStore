@extends('layouts.app')
@section('title', $product->name)
@section('content')
  <div class="bg-white p-6 rounded shadow max-w-3xl mx-auto">
    <img src="{{ $product->image ? asset('storage/'.$product->image) : 'https://via.placeholder.com/800x400' }}" class="w-full h-64 object-cover rounded mb-4">
    <h1 class="text-3xl font-bold">{{ $product->name }}</h1>
    <p>Category: {{ $product->category->name ?? 'N/A' }}</p>
    <p>Stock: {{ $product->stock_quantity }}</p>
    <p class="mt-4 text-lg">{{ $product->description ?? 'No description provided.' }}</p>
    <p class="mt-4 text-2xl font-bold text-green-600">${{ number_format($product->price, 2) }}</p>
    
    <hr>

    <h5 class="mt-4 text-xl font-bold">Comments ({{ $product->comments->count() }})</h5>

    {{-- Display comments --}}
    @forelse($product->comments as $comment)
        <div class="border p-3 my-2 rounded bg-gray-50" id="comment-{{ $comment->id }}">
            <div class="comment-view">
                <p><strong>{{ $comment->author_name }}</strong> ({{ $comment->author_email }})</p>
                <p class="comment-content">{{ $comment->content }}</p>

                <!-- <button type="button" class="text-blue-600 text-sm edit-btn">Edit</button> -->

                <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 text-sm">Delete</button>
                </form>
            </div>

            <!-- {{-- EDIT MODE  --}}
            <form action="{{ route('comments.update', $comment) }}" method="POST" class="edit-form hidden space-y-2">
                @csrf
                @method('PUT')
                <textarea name="content" class="border p-2 w-full mb-2" required>{{ old('content', $comment->content) }}</textarea>
                <div class="flex gap-2">
                    <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded">Save</button>
                    <button type="button" class="cancel-btn bg-gray-400 text-white px-3 py-1 rounded">Cancel</button>
                </div>
            </form> -->
        </div>
    @empty
        <p>No comments yet.</p>
    @endforelse


    {{-- Add new comment --}}
    <hr class="my-3">
    <h4 class="font-semibold">Add a Comment</h4>

    <form action="{{ route('products.comments.store', $product) }}" method="POST" class="space-y-2">
        @csrf
        <input type="text" name="author_name" placeholder="Your Name" class="border p-2 w-full" required>
        <input type="email" name="author_email" placeholder="Your Email" class="border p-2 w-full" required>
        <textarea name="content" placeholder="Your Comment" class="border p-2 w-full" required></textarea>
        <button type="submit" class="bg-blue-600 text-white px-3 py-2 rounded">Submit Comment</button>
    </form>




    <div class="mt-6 flex gap-3 items-center">
      <a href="{{ route('products.edit', $product) }}" class="bg-yellow-300 text-blue-800 px-4 py-2 rounded">Edit</a>

      <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Delete product?')">
        @csrf @method('DELETE')
        <button class="bg-red-500 text-white px-4 py-2 rounded">Delete</button>
      </form>

      <a href="{{ route('products.index') }}" class="text-gray-600 ml-4">← Back to products</a>
    </div>
  </div>
@endsection

