<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreReviewRequest;
use App\Http\Requests\Admin\UpdateReviewRequest;
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
        $productId = (string) $request->query('product_id', '');
        $sort = (string) $request->query('sort', 'latest');

        $query = Review::query()->with(['product:id,name', 'user:id,email,name']);

        if ($search !== '') {
            $query->leftJoin('users', 'users.id', '=', 'reviews.user_id')
                ->where('users.email', 'like', '%' . $search . '%')
                ->select('reviews.*');
        }

        $allowedRatings = ['1', '1.5', '2', '2.5', '3', '3.5', '4', '4.5', '5'];

        if ($rating !== '' && in_array($rating, $allowedRatings, true)) {
            $query->where('reviews.rating', (float) $rating);
        }

        if ($productId !== '' && ctype_digit($productId)) {
            $query->where('reviews.product_id', (int) $productId);
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
        $products = Product::query()->select(['id', 'name'])->orderBy('name')->get();

        return view('admin.reviews.index', [
            'reviews' => $reviews,
            'products' => $products,
            'filters' => [
                'search' => $search,
                'rating' => $rating,
                'product_id' => $productId,
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

    public function store(StoreReviewRequest $request)
    {
        $validated = $request->validated();

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

    public function update(UpdateReviewRequest $request, Review $review)
    {
        $validated = $request->validated();

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

    public function destroy(Request $request, Review $review)
    {
        $review->delete();

        return redirect()
            ->route('admin.reviews.index', $request->query())
            ->with('success', 'Review deleted successfully.');
    }
}
