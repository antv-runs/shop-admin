<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\ReviewServiceInterface;
use App\Http\Controllers\Controller;
use App\DTOs\ReviewAdminFilterDTO;
use App\DTOs\StoreReviewDTO;
use App\DTOs\UpdateReviewDTO;
use App\Http\Requests\Admin\StoreReviewRequest;
use App\Http\Requests\Admin\UpdateReviewRequest;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct(
        private ReviewServiceInterface $reviewService
    ) {}

    public function index(Request $request)
    {
        $filter = ReviewAdminFilterDTO::fromRequest($request);
        $reviews = $this->reviewService->paginateWithFilters($filter)->appends($request->query());
        $products = $this->reviewService->getReviewProducts();

        return view('admin.reviews.index', [
            'reviews' => $reviews,
            'products' => $products,
            'filters' => [
                'search' => $filter->search,
                'rating' => $filter->rating,
                'product_id' => $filter->productId !== null ? (string) $filter->productId : '',
                'sort' => $filter->sort,
            ],
        ]);
    }

    public function create()
    {
        $products = $this->reviewService->getReviewProducts();
        $users = $this->reviewService->getReviewUsers();

        return view('admin.reviews.create', compact('products', 'users'));
    }

    public function store(StoreReviewRequest $request)
    {
        $dto = StoreReviewDTO::fromRequest($request);
        $this->reviewService->create($dto);

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Review created successfully');
    }

    public function edit(Review $review)
    {
        $review = $this->reviewService->loadReviewRelations($review);
        $products = $this->reviewService->getReviewProducts();
        $users = $this->reviewService->getReviewUsers();

        return view('admin.reviews.edit', compact('review', 'products', 'users'));
    }

    public function update(UpdateReviewRequest $request, Review $review)
    {
        $dto = UpdateReviewDTO::fromRequest($request);
        $this->reviewService->update($review, $dto);

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Review updated successfully');
    }

    public function destroy(Request $request, Review $review)
    {
        $this->reviewService->delete($review);

        return redirect()
            ->route('admin.reviews.index', $request->query())
            ->with('success', 'Review deleted successfully.');
    }
}
