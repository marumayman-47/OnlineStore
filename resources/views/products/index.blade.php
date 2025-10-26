@extends('layouts.app')
@section('title','Products')

@section('content')
<div class="flex items-center justify-between mb-6">
  <h1 class="text-3xl font-bold text-slate-800">Products</h1>

  <!-- <div class="flex items-center gap-3">
    <a href="{{ route('products.create') }}" class="hidden sm:inline-flex items-center gap-2 bg-amber-400 px-3 py-2 rounded-lg text-amber-900 font-semibold">+ Add</a>
    <a href="{{ route('products.index') }}" class="bg-slate-100 border px-3 py-2 rounded-lg text-slate-700">Reset</a>
  </div> -->
</div>

<!-- Filters -->
<form id="filterForm" method="GET" action="{{ route('products.index') }}" class="bg-white p-4 rounded-lg shadow-sm mb-6 grid grid-cols-1 sm:grid-cols-6 gap-3">
  <div class="sm:col-span-2">
    <label class="block text-xs text-slate-600 mb-1">Search</label>
    <input type="text" name="search" value="{{ request('search') }}" id="searchInput" placeholder="Search products..." class="w-full border rounded p-2 focus:ring-2 focus:ring-indigo-300">
  </div>

  <div>
    <label class="block text-xs text-slate-600 mb-1">Category</label>
    <select name="category_id" class="w-full border rounded p-2">
      <option value="">All Categories</option>
      @foreach($categories as $cat)
        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
          {{ $cat->name }}
        </option>
      @endforeach
    </select>
  </div>

  <div>
    <label class="block text-xs text-slate-600 mb-1">Min</label>
    <input type="number" step="0.01" name="min_price" value="{{ request('min_price') }}" class="w-full border rounded p-2">
  </div>

  <div>
    <label class="block text-xs text-slate-600 mb-1">Max</label>
    <input type="number" step="0.01" name="max_price" value="{{ request('max_price') }}" class="w-full border rounded p-2">
  </div>

  <div>
    <label class="block text-xs text-slate-600 mb-1">Sort</label>
    <select name="sort" class="w-full border rounded p-2">
      <option value="">Default</option>
      <option value="price_asc" {{ request('sort')=='price_asc' ? 'selected':'' }}>Price ↑</option>
      <option value="price_desc" {{ request('sort')=='price_desc' ? 'selected':'' }}>Price ↓</option>
      <option value="newest" {{ request('sort')=='newest' ? 'selected':'' }}>Newest</option>
    </select>
  </div>
</form>

<!-- Product list partial injected here -->
<div id="productList">
  @include('products.partials.list')
</div>

<!-- AJAX script (live search + filtering + pagination + sorting) -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('filterForm');
  const productList = document.getElementById('productList');
  const searchInput = document.getElementById('searchInput');
  let timer;

  // debounce typing
  searchInput.addEventListener('input', () => {
    clearTimeout(timer);
    timer = setTimeout(() => fetchList(), 400);
  });

  // change triggers
  form.querySelectorAll('select, input[type="number"]').forEach(el => {
    el.addEventListener('change', () => fetchList());
  });

  form.addEventListener('submit', (e) => { e.preventDefault(); fetchList(); });

  function fetchList(url = "{{ route('products.index') }}") {
    const data = new FormData(form);
    const params = new URLSearchParams(data).toString();
    productList.innerHTML = '<div class="text-center p-8 text-slate-500">Loading…</div>';

    fetch(`${url}?${params}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(r => r.text())
      .then(html => {
        productList.innerHTML = html;
        attachPaginationHandlers();
      })
      .catch(() => productList.innerHTML = '<div class="text-center p-8 text-red-500">Error loading products.</div>');
  }

  function attachPaginationHandlers() {
    productList.querySelectorAll('.pagination a').forEach(link => {
      link.addEventListener('click', function (e) {
        e.preventDefault();
        fetchList(this.href);
      });
    });
  }

  attachPaginationHandlers();
});
</script>
@endsection
