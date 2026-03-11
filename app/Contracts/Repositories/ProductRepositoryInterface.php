<?php

namespace App\Contracts\Repositories;

use App\DTOs\ProductFilterDTO;

interface ProductRepositoryInterface
{
    /**
     * Find a product by ID
     */
    public function findById($id);

    /**
     * Get all products with optional filters
     */
    public function getAll(ProductFilterDTO $filter);

    /**
     * Create a new product
     */
    public function create(array $data);

    /**
     * Update a product
     */
    public function update($product, array $data);

    /**
     * Delete a product (soft delete)
     */
    public function delete($id);

    /**
     * Get trashed products
     */
    public function getTrashed($perPage = 10);

    /**
     * Restore a product
     */
    public function restore($id);

    /**
     * Force delete a product
     */
    public function forceDelete($id);

    /**
     * Paginate products
     */
    public function paginate($perPage = 10);
}
