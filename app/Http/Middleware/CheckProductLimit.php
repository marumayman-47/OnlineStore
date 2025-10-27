<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckProductLimit
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ensure user is logged in -> current user
        $user = $request->user();

        // If (not logged in), let auth middleware handle it
        if (!$user) {
            return redirect()
            ->route('login')
            ->with('error', 'You must be logged in to create a product.');
        }

        // Count how many products this user has
        $productCount = $user->products()->count();

        // Define the limit
        $limit = 5;

        // Check if limit is reached
        if ($productCount >= $limit) {
            return redirect()
                ->route('products.index')
                ->with('error', "You can only create up to {$limit} products.");
        }

        // Allow request to continue
        return $next($request);
    }
}
