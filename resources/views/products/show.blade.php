@extends('layouts.app')
@section('title', $product->name)

@section('content')
<div class="container mx-auto p-6 max-w-4xl">
  {{-- Success/Error Messages --}}
  @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded mb-4">
      {{ session('success') }}
    </div>
  @endif

  @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded mb-4">
      {{ session('error') }}
    </div>
  @endif

  {{-- Product Details Card --}}
  <div class="bg-white p-6 rounded-xl shadow-lg mb-6">
    {{-- Product Image --}}
    <img src="{{ $product->image ? asset('storage/'.$product->image) : 'https://via.placeholder.com/800x400' }}" 
         class="w-full h-64 object-cover rounded-lg mb-4">
    
    {{-- Product Info --}}
    <div class="flex items-start justify-between mb-4">
      <div class="flex-1">
        <h1 class="text-3xl font-bold text-slate-800">{{ $product->name }}</h1>
        <p class="text-gray-600 mt-1">
          <span class="font-semibold">Category:</span> {{ $product->category->name ?? 'N/A' }}
        </p>
        <p class="text-gray-600">
          <span class="font-semibold">Stock:</span> {{ $product->stock_quantity }} units
        </p>
        <p class="text-gray-600">
          <span class="font-semibold">Status:</span> 
          <span class="px-2 py-1 rounded text-xs {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
            {{ $product->is_active ? 'Active' : 'Inactive' }}
          </span>
        </p>
      </div>
      
      <div class="text-right">
        <p class="text-3xl font-bold text-green-600">${{ number_format($product->price, 2) }}</p>
      </div>
    </div>
    
    {{-- Description --}}
    <div class="border-t pt-4">
      <h3 class="font-semibold text-lg mb-2">Description</h3>
      <p class="text-gray-700">{{ $product->description ?? 'No description provided.' }}</p>
    </div>

    {{-- Action Buttons --}}
    <div class="mt-6 flex gap-3 items-center border-t pt-4">
      @auth
        @if(auth()->id() === $product->user_id || auth()->user()->is_admin ?? false)
          <a href="{{ route('products.edit', $product) }}" 
             class="bg-yellow-400 hover:bg-yellow-500 text-yellow-900 px-5 py-2 rounded-lg font-semibold transition">
            Edit Product
          </a>

          <form action="{{ route('products.destroy', $product) }}" 
                method="POST" 
                onsubmit="return confirm('Are you sure you want to delete this product?')">
            @csrf 
            @method('DELETE')
            <button class="bg-red-500 hover:bg-red-600 text-white px-5 py-2 rounded-lg font-semibold transition">
              Delete Product
            </button>
          </form>
        @else
          <div class="text-sm text-gray-500 italic">
            You don't have permission to edit this product
          </div>
        @endif
      @else
        <div class="text-sm text-gray-600 bg-gray-100 px-4 py-2 rounded-lg">
          <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">Login</a> 
          to manage this product
        </div>
      @endauth

      <a href="{{ route('products.index') }}" 
         class="text-gray-600 hover:text-gray-800 ml-auto font-semibold">
        ← Back to Products
      </a>
    </div>
  </div>

  {{-- Comments Section --}}
  <div class="bg-white p-6 rounded-xl shadow-lg">
    <h2 class="text-2xl font-bold mb-4">
      💬 Comments ({{ $product->comments->count() }})
    </h2>

    {{-- Display Comments --}}
    @forelse($product->comments as $comment)
      <div class="border-b pb-4 mb-4 last:border-b-0">
        <div class="flex items-start justify-between">
          <div class="flex-1">
            <div class="flex items-center gap-2 mb-2">
              <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold">
                {{ strtoupper(substr($comment->author_name, 0, 1)) }}
              </div>
              <div>
                <p class="font-semibold text-slate-800">{{ $comment->author_name }}</p>
                <p class="text-xs text-gray-500">{{ $comment->author_email }}</p>
              </div>
            </div>
            <p class="text-gray-700 ml-12">{{ $comment->content }}</p>
            <p class="text-xs text-gray-400 ml-12 mt-1">
              {{ $comment->created_at->diffForHumans() }}
            </p>
          </div>

          @auth
            {{-- Delete button for comment owner or admin --}}
            <form action="{{ route('comments.destroy', $comment) }}" 
                  method="POST" 
                  onsubmit="return confirm('Delete this comment?')"
                  class="ml-4">
              @csrf
              @method('DELETE')
              <button type="submit" 
                      class="text-red-600 hover:text-red-800 text-sm font-semibold">
                Delete
              </button>
            </form>
          @endauth
        </div>
      </div>
    @empty
      <p class="text-gray-500 text-center py-8">No comments yet. Be the first to comment!</p>
    @endforelse

    {{-- Add Comment Form --}}
    @auth
      <div class="mt-6 pt-6 border-t">
        <h3 class="font-semibold text-lg mb-4">Add a Comment</h3>
        
        <form action="{{ route('products.comments.store', $product) }}" 
              method="POST" 
              class="space-y-4">
          @csrf
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Your Name</label>
            <input type="text" 
                   name="author_name" 
                   value="{{ old('author_name', auth()->user()->name) }}" 
                   class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-indigo-400" 
                   required>
            @error('author_name')
              <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Your Email</label>
            <input type="email" 
                   name="author_email" 
                   value="{{ old('author_email', auth()->user()->email) }}" 
                   class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-indigo-400" 
                   required>
            @error('author_email')
              <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Your Comment</label>
            <textarea name="content" 
                      rows="4" 
                      class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-indigo-400" 
                      placeholder="Share your thoughts..."
                      required>{{ old('content') }}</textarea>
            @error('content')
              <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
          </div>

          <button type="submit" 
                  class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-semibold transition">
            Post Comment
          </button>
        </form>
      </div>
    @else
      <div class="mt-6 pt-6 border-t text-center">
        <p class="text-gray-600 mb-4">You must be logged in to comment</p>
        <a href="{{ route('login') }}" 
           class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-semibold transition">
          Login to Comment
        </a>
      </div>
    @endauth
  </div>
</div>
@endsection