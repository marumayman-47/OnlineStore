@extends('layouts.app')
@section('title','Products')

@section('content')
<div class="container mx-auto p-6">
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-3xl font-bold text-slate-800">Products</h1>

    <div class="flex items-center gap-3">
      @auth
        <a href="{{ route('products.create') }}" 
           class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow font-semibold transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          Add Product
        </a>
      @else
        <div class="text-sm text-gray-600 bg-gray-100 px-4 py-2 rounded-lg">
          <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">Login</a> 
          to add products
        </div>
      @endauth
      
      <a href="{{ route('products.index') }}" 
         class="bg-slate-100 border px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-200 transition">
        Reset Filters
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded mb-4 flex items-center">
      <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
      </svg>
      {{ session('success') }}
    </div>
  @endif

  @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded mb-4 flex items-center">
      <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
      </svg>
      {{ session('error') }}
    </div>
  @endif

  <!-- Filters -->
  <form id="filterForm" method="GET" action="{{ route('products.index') }}" 
        class="bg-white p-4 rounded-lg shadow-sm mb-6 grid grid-cols-1 sm:grid-cols-6 gap-3">
    
    <div class="sm:col-span-2">
      <label class="block text-xs text-slate-600 mb-1">Search</label>
      <input type="text" name="search" value="{{ request('search') }}" 
             id="searchInput" placeholder="Search products..." 
             class="w-full border rounded p-2 focus:ring-2 focus:ring-indigo-300">
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
      <label class="block text-xs text-slate-600 mb-1">Min Price</label>
      <input type="number" step="0.01" name="min_price" value="{{ request('min_price') }}" 
             class="w-full border rounded p-2">
    </div>

    <div>
      <label class="block text-xs text-slate-600 mb-1">Max Price</label>
      <input type="number" step="0.01" name="max_price" value="{{ request('max_price') }}" 
             class="w-full border rounded p-2">
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

  <!-- Product list -->
  <div id="productList">
    @include('products.partials.list')
  </div>
</div>

<!-- AJAX script -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('filterForm');
  const productList = document.getElementById('productList');
  const searchInput = document.getElementById('searchInput');
  let timer;

  searchInput.addEventListener('input', () => {
    clearTimeout(timer);
    timer = setTimeout(() => fetchList(), 400);
  });

  form.querySelectorAll('select, input[type="number"]').forEach(el => {
    el.addEventListener('change', () => fetchList());
  });

  form.addEventListener('submit', (e) => { 
    e.preventDefault(); 
    fetchList(); 
  });

  function fetchList(url = "{{ route('products.index') }}") {
    const data = new FormData(form);
    const params = new URLSearchParams(data).toString();
    productList.innerHTML = '<div class="text-center p-8 text-slate-500">Loading…</div>';

    fetch(`${url}?${params}`, { 
      headers: { 'X-Requested-With': 'XMLHttpRequest' } 
    })
      .then(r => r.text())
      .then(html => {
        productList.innerHTML = html;
        attachPaginationHandlers();
      })
      .catch(() => {
        productList.innerHTML = '<div class="text-center p-8 text-red-500">Error loading products.</div>';
      });
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