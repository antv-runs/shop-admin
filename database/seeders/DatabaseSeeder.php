<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Create default admin
        if (!User::where('email', 'uter.vanan@gmail.com')->exists()) {
            User::create([
                'name' => 'Admin',
                'email' => 'uter.vanan@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]);
        }

        $this->createUsers();
        $this->createCategories();
        $this->createProducts();
        $this->createOrders();
    }

    public function createUsers()
    {
        $targetEmail = 'vanantran05@gmail.com';
        if (!User::where('email', $targetEmail)->exists()) {
            User::create([
                'name' => "An 05",
                'email' => $targetEmail,
                'password' => Hash::make('password'),
                'role' => 'user',
            ]);
        }


        for ($i = 1; $i <= 20; $i++) {
            $email = "user{$i}@example.com";

            if (!User::where('email', $email)->exists()) {
                User::create([
                    'name' => "User {$i}",
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'role' => 'user',
                ]);
            }
        }
    }

    public function createCategories()
    {
        $categories = [
            'Shop',
            'On Sale',
            'New Arrivals',
            'Brands',
            'Casual',
            'Formal',
            'Party',
            'Gym',
            'T-shirts',
            'Polo Shirts',
            'Shirts',
            'Jeans',
            'Shorts',
            'Hoodies',
            'Outerwear',
            'Sweaters',
            'Blazers',
            'Joggers',
            'Accessories',
            'Footwear',
        ];

        foreach ($categories as $categoryName) {
            Category::firstOrCreate(
                ['slug' => Str::slug($categoryName)],
                [
                    'name' => $categoryName,
                    'description' => "Category for {$categoryName} products"
                ]
            );
        }
    }

    public function createProducts()
    {
        $categories = Category::query()->select(['id', 'name', 'slug'])->get();

        if ($categories->isEmpty()) {
            return;
        }

        $categoryBySlug = $categories->keyBy('slug');

        $resolveCategoryId = function (array $preferredSlugs) use ($categoryBySlug, $categories) {
            foreach ($preferredSlugs as $slug) {
                if ($categoryBySlug->has($slug)) {
                    return $categoryBySlug->get($slug)->id;
                }
            }

            return $categories->random()->id;
        };

        $baseProducts = [
            [
                'name' => 'One Life Graphic T-shirt',
                'price' => 260,
                'image' => 'pic_t-shirt-main-1.png',
                'description' => 'Graphic cotton t-shirt inspired by the featured product detail page.',
                'category_slugs' => ['t-shirts', 'casual', 'shop'],
            ],
            [
                'name' => 'Polo with Contrast Trims',
                'price' => 212,
                'image' => 'pic_polo_blue.png',
                'description' => 'Classic polo with contrast details from the cart and product list UI.',
                'category_slugs' => ['polo-shirts', 'casual', 'shop'],
            ],
            [
                'name' => 'Gradient Graphic T-shirt',
                'price' => 145,
                'image' => 'pic_t_shirt.png',
                'description' => 'Gradient print t-shirt highlighted in catalog and cart views.',
                'category_slugs' => ['t-shirts', 'casual', 'shop'],
            ],
            [
                'name' => 'Polo with Tipping Details',
                'price' => 180,
                'image' => 'pic_polo_tipping.png',
                'description' => 'Minimal tipping-detail polo showcased in the catalog grid.',
                'category_slugs' => ['polo-shirts', 'casual', 'shop'],
            ],
            [
                'name' => 'Black Striped T-shirt',
                'price' => 120,
                'image' => 'pic_t_shirt_black.png',
                'description' => 'Black striped tee featured across catalog and cart with promo price.',
                'category_slugs' => ['t-shirts', 'casual', 'on-sale'],
            ],
            [
                'name' => 'Skinny Fit Jeans',
                'price' => 240,
                'image' => 'product4.png',
                'description' => 'Slim-cut denim jeans displayed in the product list section.',
                'category_slugs' => ['jeans', 'casual', 'shop'],
            ],
            [
                'name' => 'Checkered Shirt',
                'price' => 180,
                'image' => 'product5.png',
                'description' => 'Checkered shirt from the homepage catalog examples.',
                'category_slugs' => ['shirts', 'casual', 'new-arrivals'],
            ],
            [
                'name' => 'Sleeve Striped T-shirt',
                'price' => 130,
                'image' => 'product6.png',
                'description' => 'Sleeve-striped t-shirt featured in the UI product tiles.',
                'category_slugs' => ['t-shirts', 'casual', 'shop'],
            ],
        ];

        foreach ($baseProducts as $product) {
            Product::firstOrCreate(
                ['slug' => Str::slug($product['name'])],
                [
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'description' => $product['description'],
                    'category_id' => $resolveCategoryId($product['category_slugs']),
                    'image' => $product['image'],
                ]
            );
        }

        $targetProductCount = 180;
        $existingCount = Product::count();
        $remaining = max(0, $targetProductCount - $existingCount);

        if ($remaining === 0) {
            return;
        }

        $styleWords = [
            'Classic',
            'Modern',
            'Urban',
            'Premium',
            'Essential',
            'Relaxed',
            'Slim Fit',
            'Oversized',
            'Vintage',
            'Sport',
            'Everyday',
            'Signature',
        ];

        $materials = [
            'Cotton',
            'Denim',
            'Linen',
            'Fleece',
            'Knit',
            'Jersey',
            'Twill',
            'Performance',
        ];

        $productTypeByCategorySlug = [
            't-shirts' => ['Graphic T-shirt', 'Crew Neck Tee', 'Striped T-shirt'],
            'polo-shirts' => ['Polo Shirt', 'Textured Polo', 'Contrast Polo'],
            'shirts' => ['Oxford Shirt', 'Checkered Shirt', 'Casual Shirt'],
            'jeans' => ['Slim Jeans', 'Straight Jeans', 'Relaxed Jeans'],
            'shorts' => ['Cargo Shorts', 'Cotton Shorts', 'Denim Shorts'],
            'hoodies' => ['Pullover Hoodie', 'Zip Hoodie', 'Fleece Hoodie'],
            'outerwear' => ['Bomber Jacket', 'Lightweight Jacket', 'Windbreaker'],
            'sweaters' => ['Crewneck Sweater', 'Knit Sweater', 'Cardigan'],
            'blazers' => ['Tailored Blazer', 'Classic Blazer', 'Soft Blazer'],
            'joggers' => ['Slim Joggers', 'Relaxed Joggers', 'Training Joggers'],
            'accessories' => ['Canvas Belt', 'Minimal Cap', 'Travel Tote'],
            'footwear' => ['Street Sneakers', 'Classic Loafers', 'Running Shoes'],
            'casual' => ['Casual Tee', 'Everyday Shirt', 'Comfort Hoodie'],
            'formal' => ['Formal Shirt', 'Dress Trousers', 'Suit Blazer'],
            'party' => ['Party Shirt', 'Night Blazer', 'Statement Tee'],
            'gym' => ['Training Tee', 'Gym Shorts', 'Performance Joggers'],
            'shop' => ['Lifestyle Tee', 'Core Polo', 'Modern Jeans'],
            'on-sale' => ['Sale Tee', 'Sale Polo', 'Sale Jeans'],
            'new-arrivals' => ['New Arrival Tee', 'New Arrival Shirt', 'New Arrival Hoodie'],
            'brands' => ['Signature Tee', 'Brand Polo', 'Brand Jeans'],
        ];

        $placeholderImages = [
            'default-product.jpg',
            'product1.png',
            'product2.png',
            'product3.png',
            'product4.png',
            'product5.png',
            'product6.png',
        ];

        for ($i = 1; $i <= $remaining; $i++) {
            $category = $categories[($i - 1) % $categories->count()];
            $categorySlug = $category->slug;

            $style = Arr::random($styleWords);
            $material = Arr::random($materials);
            $types = $productTypeByCategorySlug[$categorySlug] ?? ['Fashion Item'];
            $type = Arr::random($types);

            $suffix = str_pad((string) $i, 3, '0', STR_PAD_LEFT);
            $name = "{$style} {$type} {$material} {$suffix}";

            Product::firstOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'price' => rand(80, 320),
                    'description' => "{$name} for {$category->name} looks, designed for daily comfort and versatile styling.",
                    'category_id' => $category->id,
                    'image' => Arr::random($placeholderImages),
                ]
            );
        }
    }

    public function createOrders()
    {
        $users = User::where('role', 'user')->get();
        $products = Product::query()->select(['id', 'price'])->get();

        if ($users->isEmpty() || $products->isEmpty()) {
            return;
        }

        $targetOrderCount = 40;
        $existingOrderCount = Order::count();
        $remaining = max(0, $targetOrderCount - $existingOrderCount);

        if ($remaining === 0) {
            return;
        }

        for ($i = 0; $i < $remaining; $i++) {
            DB::transaction(function () use ($users, $products) {
                $order = Order::create([
                    'user_id' => $users->random()->id,
                    'total_amount' => 0,
                    'status' => Arr::random(['pending', 'processing', 'completed']),
                ]);

                $totalAmount = 0;
                $itemsCount = rand(1, 5);
                $selectedProducts = $products->shuffle()->take($itemsCount);

                foreach ($selectedProducts as $product) {
                    $quantity = rand(1, 4);
                    $price = (float) $product->price;
                    $lineTotal = $price * $quantity;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'price' => $price,
                        'total' => $lineTotal,
                    ]);

                    $totalAmount += $lineTotal;
                }

                $order->update([
                    'total_amount' => $totalAmount,
                ]);
            });
        }
    }
}
