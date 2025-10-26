<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Product;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'content'      => 'required|string|max:1000',
            'author_name'  => 'required|string|max:255',
            'author_email' => 'required|email|max:255',
        ]);

        $product->comments()->create($validated);

        return redirect()->back()->with('success', 'Comment added successfully.');
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();

        return redirect()->back()->with('success', 'Comment deleted.');
    }

}
