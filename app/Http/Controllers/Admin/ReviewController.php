<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $rating = (string) $request->query('rating', '');
        $sort = (string) $request->query('sort', 'latest');

        $query = Review::query()->with(['product:id,name', 'user:id,email,name']);

        if ($search !== '') {
            $query->leftJoin('users', 'users.id', '=', 'reviews.user_id')
                ->where('users.email', 'like', '%' . $search . '%')
                ->select('reviews.*');
        }

        if ($rating !== '' && in_array($rating, ['1', '2', '3', '4', '5'], true)) {
            $query->where('reviews.rating', (float) $rating);
        }

        if ($sort === 'oldest') {
            $query->orderBy('reviews.created_at', 'asc');
        } elseif ($sort === 'highest') {
            $query->orderBy('reviews.rating', 'desc')->orderBy('reviews.created_at', 'desc');
        } else {
            $sort = 'latest';
            $query->orderBy('reviews.created_at', 'desc');
        }

        $reviews = $query->paginate(10)->appends($request->query());

        return view('admin.reviews.index', [
            'reviews' => $reviews,
            'filters' => [
                'search' => $search,
                'rating' => $rating,
                'sort' => $sort,
            ],
        ]);
    }

    public function create()
    {
        $products = Product::query()->select(['id', 'name'])->orderBy('name')->get();
        $users = User::query()->select(['id', 'name', 'email'])->orderBy('name')->get();

        return view('admin.reviews.create', compact('products', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'rating' => ['required', 'numeric', 'min:1', 'max:5'],
            'comment' => ['required', 'string'],
            'is_verified' => ['nullable', 'boolean'],
        ]);

        Review::create([
            'product_id' => (int) $validated['product_id'],
            'user_id' => $validated['user_id'] ?? null,
            'rating' => (float) $validated['rating'],
            'comment' => $validated['comment'],
            'is_verified' => $request->boolean('is_verified'),
        ]);

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Review created successfully');
    }

    public function edit(Review $review)
    {
        $review->load(['product:id,name', 'user:id,name,email']);
        $products = Product::query()->select(['id', 'name'])->orderBy('name')->get();
        $users = User::query()->select(['id', 'name', 'email'])->orderBy('name')->get();

        return view('admin.reviews.edit', compact('review', 'products', 'users'));
    }

    public function update(Request $request, Review $review)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'rating' => ['required', 'numeric', 'min:1', 'max:5'],
            'comment' => ['required', 'string'],
            'is_verified' => ['nullable', 'boolean'],
        ]);

        $review->update([
            'product_id' => (int) $validated['product_id'],
            'user_id' => $validated['user_id'] ?? null,
            'rating' => (float) $validated['rating'],
            'comment' => $validated['comment'],
            'is_verified' => $request->boolean('is_verified'),
        ]);

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Review updated successfully');
    }
}
