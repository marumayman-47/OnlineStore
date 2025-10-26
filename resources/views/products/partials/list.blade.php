<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
  @forelse($products as $product)
    <div class="relative bg-white rounded-2xl shadow hover:shadow-2xl transition overflow-hidden">
      {{-- Provisional ribbon when product is inactive --}}
      @unless($product->is_active)
        <div class="ribbon">Provisional</div>
      @endunless

      <div class="h-48 w-full bg-gray-100">
        <img src="{{ $product->image ? asset('storage/'.$product->image) : 'https://via.placeholder.com/600x400?text=No+Image' }}"
             alt="{{ $product->name }}" class="w-full h-full object-cover">
      </div>

      <div class="p-4">
        <h3 class="font-semibold text-lg text-slate-800">{{ $product->name }}</h3>
        <p class="text-slate-600 text-sm">
          Category: <span class="font-semibold">{{ $product->category->name ?? 'Uncategorized' }}</span>
        </p>

        <h6 class="mt-4 text-xl font-bold">💬 Comments ({{ $product->comments->count() }})</h6>

        <div class="flex items-center justify-between mt-3">
          <div class="text-lg font-bold text-emerald-600">${{ number_format($product->price,2) }}</div>
          <div class="text-xs text-slate-500">Stock: {{ $product->stock_quantity }}</div>
        </div>

        <div class="mt-3 flex gap-2">
          <a href="{{ route('products.show',$product) }}" class="inline-flex items-center gap-2 px-3 py-1 rounded bg-indigo-600 text-white text-sm hover:bg-indigo-700">
            View
          </a>
          <a href="{{ route('products.edit',$product) }}" class="inline-flex items-center gap-2 px-3 py-1 rounded bg-amber-100 text-amber-800 text-sm hover:bg-amber-200">
            Edit
          </a>

          <form action="{{ route('products.destroy',$product) }}" method="POST" onsubmit="return confirm('Delete this product?')" class="ml-auto">
            @csrf @method('DELETE')
            <button type="submit" class="text-red-600 text-sm hover:underline">Delete</button>
          </form>
        </div>
      </div>
    </div>
  @empty
    <div class="col-span-full text-center p-8 text-slate-500">No products found.</div>
  @endforelse
</div>

<!-- Pagination (Tailwind default) -->
<div class="mt-6 flex justify-center">
  {{ $products->links() }}
</div>
