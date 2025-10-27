<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;


Route::get('/', fn() => redirect()->route('products.index'));


Route::middleware(['auth'])->group(function () {

    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);

    // Comment routes
    Route::post('products/{product}/comments', [CommentController::class, 'store'])->name('products.comments.store');
    Route::delete('comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::put('comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
});

// Dashboard
Route::get('/dashboard', function () {
    return redirect()->route('products.index');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile routes from Breeze
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Auth routes 
require __DIR__.'/auth.php';
