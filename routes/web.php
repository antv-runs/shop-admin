<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    // If user is authenticated, send them to the right dashboard.
    if (auth()->check()) {
        $user = auth()->user();
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return redirect('/admin');
        }

        return redirect('/dashboard');
    }

    // Otherwise, redirect unauthenticated visitors to the login page
    return redirect()->route('login');
});

use App\Models\Product;

Route::get('/dashboard', function () {
    // show product list to regular users (read-only)
    $products = Product::with('category')->latest()->paginate(10);
    return view('dashboard', compact('products'));
})->middleware(['auth'])->name('dashboard');

// User Profile routes
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/image', [\App\Http\Controllers\ProfileController::class, 'deleteImage'])->name('profile.deleteImage');
});

// Admin routes
use App\Http\Controllers\Admin\AdminController;

Route::prefix('admin')->middleware(['auth','is_admin'])->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');

    // Admin Profile routes
    Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'show'])->name('admin.profile.show');
    Route::get('/profile/edit', [\App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('admin.profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('admin.profile.update');
    Route::delete('/profile/image', [\App\Http\Controllers\Admin\ProfileController::class, 'deleteImage'])->name('admin.profile.deleteImage');

    // Soft delete routes (MUST be before resource routes)
    Route::get('/users/trashed', [\App\Http\Controllers\Admin\UserController::class, 'trashed'])->name('admin.users.trashed');
    Route::patch('/users/{id}/restore', [\App\Http\Controllers\Admin\UserController::class, 'restore'])->name('admin.users.restore');
    Route::delete('/users/{id}/force-delete', [\App\Http\Controllers\Admin\UserController::class, 'forceDelete'])->name('admin.users.forceDelete');

    Route::get('/products/trashed', [\App\Http\Controllers\Admin\ProductController::class, 'trashed'])->name('admin.products.trashed');
    Route::patch('/products/{id}/restore', [\App\Http\Controllers\Admin\ProductController::class, 'restore'])->name('admin.products.restore');
    Route::delete('/products/{id}/force-delete', [\App\Http\Controllers\Admin\ProductController::class, 'forceDelete'])->name('admin.products.forceDelete');

    Route::get('/categories/trashed', [\App\Http\Controllers\Admin\CategoryController::class, 'trashed'])->name('admin.categories.trashed');
    Route::patch('/categories/{id}/restore', [\App\Http\Controllers\Admin\CategoryController::class, 'restore'])->name('admin.categories.restore');
    Route::delete('/categories/{id}/force-delete', [\App\Http\Controllers\Admin\CategoryController::class, 'forceDelete'])->name('admin.categories.forceDelete');

    // Admin resource routes
    Route::resource('products', \App\Http\Controllers\Admin\ProductController::class, ['as' => 'admin']);
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class, ['as' => 'admin']);
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class, ['as' => 'admin']);
});

require __DIR__.'/auth.php';
