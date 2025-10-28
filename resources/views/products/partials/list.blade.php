<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
  @forelse($products as $product)
    <div class="relative bg-white rounded-2xl shadow hover:shadow-2xl transition overflow-hidden">
      {{-- Provisional ribbon when product is inactive --}}
      @unless($product->is_active)
        <div class="absolute top-2 right-2 bg-yellow-400 text-yellow-900 text-xs font-bold px-3 py-1 rounded-full shadow-lg z-10">
          Inactive
        </div>
      @endunless

      {{-- Product Image --}}
      <div class="h-48 w-full bg-gray-100">
        <img src="{{ $product->image ? asset('storage/'.$product->image) : 'https://via.placeholder.com/600x400?text=No+Image' }}"
             alt="{{ $product->name }}" 
             class="w-full h-full object-cover">
      </div>

      {{-- Product Details --}}
      <div class="p-4">
        <h3 class="font-semibold text-lg text-slate-800 line-clamp-1">{{ $product->name }}</h3>
        <p class="text-slate-600 text-sm">
          Category: <span class="font-semibold">{{ $product->category->name ?? 'Uncategorized' }}</span>
        </p>

        <div class="flex items-center justify-between mt-3">
          <div class="text-xl font-bold text-emerald-600">${{ number_format($product->price, 2) }}</div>
          <div class="text-xs text-slate-500">Stock: {{ $product->stock_quantity }}</div>
        </div>

        <div class="text-sm text-gray-500 mt-2">
          💬 {{ $product->comments->count() }} {{ Str::plural('comment', $product->comments->count()) }}
        </div>

        {{-- Action Buttons --}}
        <div class="mt-3 flex gap-2 flex-wrap">
          {{-- View button - always visible --}}
          <a href="{{ route('products.show', $product) }}" 
             class="inline-flex items-center gap-1 px-3 py-1.5 rounded bg-indigo-600 text-white text-sm hover:bg-indigo-700 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            View
          </a>

          @auth
            {{-- Edit button - only for product owner or admin --}}
            @if(auth()->id() === $product->user_id || auth()->user()->is_admin ?? false)
              <a href="{{ route('products.edit', $product) }}" 
                 class="inline-flex items-center gap-1 px-3 py-1.5 rounded bg-amber-100 text-amber-800 text-sm hover:bg-amber-200 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
              </a>

              {{-- Delete button - only for product owner or admin --}}
              <form action="{{ route('products.destroy', $product) }}" 
                    method="POST" 
                    onsubmit="return confirm('Are you sure you want to delete this product?')" 
                    class="inline">
                @csrf 
                @method('DELETE')
                <button type="submit" 
                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded bg-red-100 text-red-700 text-sm hover:bg-red-200 transition">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
                  Delete
                </button>
              </form>
            @endif
          @else
            {{-- Guest message --}}
            <div class="text-xs text-gray-500 italic ml-auto">
              <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-800">Login</a> to manage
            </div>
          @endauth
        </div>
      </div>
    </div>
  @empty
    <div class="col-span-full text-center p-12 bg-gray-50 rounded-lg">
      <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
      </svg>
      <p class="text-slate-500 text-lg">No products found.</p>
      @auth
        <a href="{{ route('products.create') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold mt-2 inline-block">
          Create your first product
        </a>
      @endauth
    </div>
  @endforelse
</div>

<!-- Pagination -->
<div class="mt-6 flex justify-center">
  {{ $products->links() }}
</div>