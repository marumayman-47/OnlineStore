@extends('layouts.app')

@section('content')
<div class="max-w-lg mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Edit Comment</h2>

    <form action="{{ route('comments.update', $comment) }}" method="POST" class="space-y-3">
        @csrf
        @method('PUT')

        <div>
            <label class="block font-semibold">Name:</label>
            <input type="text" name="author_name" value="{{ old('author_name', $comment->author_name) }}" class="border p-2 w-full" required>
        </div>

        <div>
            <label class="block font-semibold">Email:</label>
            <input type="email" name="author_email" value="{{ old('author_email', $comment->author_email) }}" class="border p-2 w-full" required>
        </div>

        <div>
            <label class="block font-semibold">Comment:</label>
            <textarea name="content" class="border p-2 w-full" required>{{ old('content', $comment->content) }}</textarea>
        </div>

        <div class="flex justify-between mt-3">
            <a href="{{ route('products.show', $comment->commentable_id) }}" class="text-gray-600">← Back</a>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Update Comment</button>
        </div>
    </form>
</div>
@endsection
