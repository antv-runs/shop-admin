<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\ProductIndexRequest;
use App\Contracts\ProductServiceInterface;
use App\DTOs\CreateProductDTO;
use App\DTOs\UpdateProductDTO;
use App\DTOs\ProductFilterDTO;
use App\DTOs\UploadImageDTO;
use App\Exceptions\BusinessException;

class ProductController extends Controller
{
    private ProductServiceInterface $productService;

    public function __construct(ProductServiceInterface $productService)
    {
        $this->productService = $productService;
    }

    public function index(ProductIndexRequest $request)
    {
        $filter = ProductFilterDTO::fromRequest($request);
        $products = $this->productService->getAllProducts($filter);
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
        $validated = $request->validated();

        // Create DTO from validated data
        $dto = CreateProductDTO::fromArray($validated);

        $product = $this->productService->createProduct($dto);

        if ($request->hasFile('images')) {
            $this->productService->uploadProductImage(UploadImageDTO::fromArray([
                'id' => $product->id,
                'images' => $request->file('images', []),
            ]));
        }

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
        $imageAction = $request->input('image_action');
        $imageId = (int) $request->input('image_id', 0);

        if (!empty($imageAction) && $imageId > 0) {
            switch ($imageAction) {
                case 'set_primary':
                    $this->productService->setPrimaryProductImage((int) $product->id, $imageId);
                    return redirect()->route('admin.products.edit', $product->id)
                        ->with('success', 'Primary image updated successfully');
                case 'move_left':
                    $this->productService->moveProductImageLeft((int) $product->id, $imageId);
                    return redirect()->route('admin.products.edit', $product->id)
                        ->with('success', 'Image moved left successfully');
                case 'move_right':
                    $this->productService->moveProductImageRight((int) $product->id, $imageId);
                    return redirect()->route('admin.products.edit', $product->id)
                        ->with('success', 'Image moved right successfully');
                case 'delete_image':
                    $this->productService->deleteProductImage((int) $product->id, $imageId);
                    return redirect()->route('admin.products.edit', $product->id)
                        ->with('success', 'Image deleted successfully');
            }
        }

        $validated = $request->validated();

        // Create DTO from validated data
        $dto = UpdateProductDTO::fromArray($validated);

        $this->productService->updateProduct($product, $dto);

        if ($request->hasFile('images')) {
            $this->productService->uploadProductImage(UploadImageDTO::fromArray([
                'id' => $product->id,
                'images' => $request->file('images', []),
            ]));
        }

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
