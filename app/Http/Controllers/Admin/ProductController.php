<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Contracts\ProductServiceInterface;
use App\Contracts\FileUploadServiceInterface;
use App\Exceptions\BusinessException;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * @var ProductServiceInterface
     */
    private $productService;

    /**
     * @var FileUploadServiceInterface
     */
    private $fileUploadService;

    /**
     * Inject dependencies
     *
     * Dependencies: ProductServiceInterface, FileUploadServiceInterface
     * Follows Dependency Inversion - depends on abstractions not concretions
     */
    public function __construct(ProductServiceInterface $productService, FileUploadServiceInterface $fileUploadService)
    {
        $this->productService = $productService;
        $this->fileUploadService = $fileUploadService;
    }

    public function index(Request $request)
    {
        $perPage = 10;
        $products = $this->productService->getAllProducts($request, $perPage);
        $categories = $this->productService->getCategories();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = $this->productService->getCategories();
        return view('admin.products.create', compact('categories'));
    }

    public function store(ProductRequest $request)
    {
        $data = $request->validated();

        // Handle image upload - delegated to FileUploadService
        // Single Responsibility: controller doesn't handle file operations
        if ($request->hasFile('image')) {
            $data['image'] = $this->fileUploadService->uploadProductImage($request->file('image'));
        }

        $this->productService->createProduct($data);

        return redirect()->route('admin.products.index')
             ->with('success', 'Product created successfully');
    }

    public function edit($id)
    {
        $product = $this->productService->getProduct($id);
        $categories = $this->productService->getCategories();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(ProductRequest $request, $id)
    {
        $product = $this->productService->getProduct($id);
        $data = $request->validated();

        // Handle image upload - delegated to FileUploadService
        // Single Responsibility: controller doesn't handle file operations
        if ($request->hasFile('image')) {
            $data['image'] = $this->fileUploadService->replaceFile($product->image, $request->file('image'));
        }

        $this->productService->updateProduct($product, $data);

        return redirect()->route('admin.products.index')
             ->with('success', 'Product updated successfully');
    }

    public function destroy($id)
    {
        $this->productService->deleteProduct($id);

        return redirect()->route('admin.products.index')
             ->with('success', 'Product deleted successfully');
    }

    /**
     * Show trashed products
     */
    public function trashed()
    {
        $products = $this->productService->getTrashed(10);
        return view('admin.products.trashed', compact('products'));
    }

    /**
     * Restore product
     */
    public function restore($id)
    {
        try {
            $this->productService->restoreProduct($id);
            return redirect()->route('admin.products.trashed')->with('success', 'Product restored successfully');
        } catch (BusinessException $e) {
            return redirect()->route('admin.products.trashed')->with('error', $e->getMessage());
        }
    }

    /**
     * Force delete product
     */
    public function forceDelete($id)
    {
        $this->productService->forceDeleteProduct($id);
        return redirect()->route('admin.products.trashed')->with('success', 'Product permanently deleted');
    }
}
