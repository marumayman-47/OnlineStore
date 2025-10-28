@props(['type' => 'primary', 'href' => '#', 'icon' => null, 'text' => '', 'method' => 'GET'])

@php
    $baseClasses = "inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg shadow transition duration-200";

    $typeClasses = match($type) {
        'primary' => 'bg-blue-600 hover:bg-blue-700 text-white',
        'secondary' => 'bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:text-gray-100 dark:hover:bg-gray-600',
        'danger' => 'bg-red-600 hover:bg-red-700 text-white',
        'success' => 'bg-green-600 hover:bg-green-700 text-white',
        'warning' => 'bg-yellow-500 hover:bg-yellow-600 text-white',
        default => 'bg-gray-500 hover:bg-gray-600 text-white'
    };
@endphp

@if (strtoupper($method) === 'GET')
    <a href="{{ $href }}" class="{{ $baseClasses }} {{ $typeClasses }}">
        @if($icon)
            <span>{!! $icon !!}</span>
        @endif
        <span>{{ $text }}</span>
    </a>
@else
    <form action="{{ $href }}" method="POST" class="inline">
        @csrf
        @method($method)
        <button type="submit" class="{{ $baseClasses }} {{ $typeClasses }}"
                onclick="return confirm('هل أنت متأكد من تنفيذ هذا الإجراء؟')">
            @if($icon)
                <span>{!! $icon !!}</span>
            @endif
            <span>{{ $text }}</span>
        </button>
    </form>
@endif
