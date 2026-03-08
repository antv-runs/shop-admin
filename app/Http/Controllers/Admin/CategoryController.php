<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use App\Contracts\CategoryServiceInterface;
use App\DTOs\CreateCategoryDTO;
use App\DTOs\UpdateCategoryDTO;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * @var CategoryServiceInterface
     */
    private $categoryService;

    /**
     * Inject CategoryServiceInterface
     */
    public function __construct(CategoryServiceInterface $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index(\Illuminate\Http\Request $request)
    {
        $perPage = 15;
        $categories = $this->categoryService->getAllCategories($request, $perPage);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(CategoryRequest $request)
    {
        $validated = $request->validated();
        
        // Create DTO from validated data
        $dto = CreateCategoryDTO::fromArray($validated);
        
        $this->categoryService->createCategory($dto);

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully');
    }

    public function edit($id)
    {
        $category = $this->categoryService->getCategory($id);
        return view('admin.categories.edit', compact('category'));
    }

    public function update(CategoryRequest $request, $id)
    {
        $category = $this->categoryService->getCategory($id);
        $validated = $request->validated();
        
        // Create DTO from validated data
        $dto = UpdateCategoryDTO::fromArray($validated);
        
        $this->categoryService->updateCategory($category, $dto);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully');
    }

    public function destroy($id)
    {
        $this->categoryService->deleteCategory($id);
        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully');
    }

    /**
     * Show trashed categories
     */
    public function trashed()
    {
        $categories = $this->categoryService->getTrashed(15);
        return view('admin.categories.trashed', compact('categories'));
    }

    /**
     * Restore category
     */
    public function restore($id)
    {
        try {
            $this->categoryService->restoreCategory($id);
            return redirect()->route('admin.categories.trashed')->with('success', 'Category restored successfully');
        } catch (\Exception $e) {
            return redirect()->route('admin.categories.trashed')->with('error', $e->getMessage());
        }
    }

    /**
     * Force delete category
     */
    public function forceDelete($id)
    {
        $this->categoryService->forceDeleteCategory($id);
        return redirect()->route('admin.categories.trashed')->with('success', 'Category permanently deleted');
    }
}
