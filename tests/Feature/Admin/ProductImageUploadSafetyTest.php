<?php

namespace Tests\Feature\Admin;

use App\Contracts\Repositories\CategoryRepositoryInterface;
use App\Contracts\Repositories\ProductRepositoryInterface;
use App\DTOs\UploadImageDTO;
use App\Enums\UserRole;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use App\Services\FileUploadService;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class ProductImageUploadSafetyTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_rejects_more_than_ten_images(): void
    {
        Storage::fake('s3');

        $admin = User::query()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => UserRole::ADMIN->value,
        ]);

        $images = [];
        for ($index = 0; $index < 11; $index++) {
            $images[] = UploadedFile::fake()->image("image-{$index}.jpg")->size(200);
        }

        $response = $this->actingAs($admin)->post(route('admin.products.store'), [
            'name' => 'Safety Test Product',
            'price' => '100',
            'currency' => 'USD',
            'is_active' => '1',
            'images' => $images,
        ]);

        $response->assertSessionHasErrors(['images']);
        $this->assertDatabaseCount('product_images', 0);
    }

    public function test_upload_cleanup_removes_files_when_insert_fails_mid_batch(): void
    {
        Storage::fake('s3');
        config(['filesystems.default' => 's3']);

        $product = Product::query()->create([
            'name' => 'Cleanup Product',
            'price' => 10,
            'compare_price' => null,
            'description' => null,
            'details' => null,
            'colors' => [],
            'sizes' => [],
            'currency' => 'USD',
            'is_active' => true,
            'category_id' => null,
        ]);

        $productRepository = Mockery::mock(ProductRepositoryInterface::class);
        $categoryRepository = Mockery::mock(CategoryRepositoryInterface::class);

        $productRepository
            ->shouldReceive('findById')
            ->once()
            ->with($product->id)
            ->andReturn($product->fresh()->load(['images', 'primaryImage', 'category']));

        $createCalls = 0;
        $productRepository
            ->shouldReceive('createProductImage')
            ->twice()
            ->andReturnUsing(function ($productId, $path, $isPrimary) use (&$createCalls) {
                $createCalls++;

                if ($createCalls === 2) {
                    throw new RuntimeException('Simulated DB insert failure');
                }

                return ProductImage::query()->create([
                    'product_id' => $productId,
                    'image_url' => $path,
                    'sort_order' => 1,
                    'is_primary' => (bool) $isPrimary,
                ]);
            });

        $service = new ProductService(
            new FileUploadService(),
            $productRepository,
            $categoryRepository
        );

        $dto = new UploadImageDTO($product->id, [
            UploadedFile::fake()->image('first.jpg')->size(200),
            UploadedFile::fake()->image('second.jpg')->size(200),
        ]);

        $this->expectException(RuntimeException::class);

        try {
            $service->uploadProductImage($dto);
        } finally {
            $this->assertDatabaseCount('product_images', 0);
            $this->assertSame([], Storage::disk('s3')->allFiles('products'));
        }
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
