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
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

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
        $this->createReviews();
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

        $reviewUserNames = [
            'Samantha D.',
            'Alex M.',
            'Ethan R.',
            'Olivia P.',
            'Liam K.',
            'Ava H.',
            'Noah T.',
            'Mia L.',
        ];

        foreach ($reviewUserNames as $name) {
            $email = Str::slug($name, '.') . '@example.com';

            User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => bcrypt('password'),
                    'role' => 'user',
                ]
            );
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
                'compare_price' => 300,
                'description' => 'Graphic cotton t-shirt inspired by the featured product detail page.',
                'category_slugs' => ['t-shirts', 'casual', 'shop'],
            ],
            [
                'name' => 'Polo with Contrast Trims',
                'price' => 212,
                'description' => 'Classic polo with contrast details from the cart and product list UI.',
                'category_slugs' => ['polo-shirts', 'casual', 'shop'],
            ],
            [
                'name' => 'Gradient Graphic T-shirt',
                'price' => 145,
                'description' => 'Gradient print t-shirt highlighted in catalog and cart views.',
                'category_slugs' => ['t-shirts', 'casual', 'shop'],
            ],
            [
                'name' => 'Polo with Tipping Details',
                'price' => 180,
                'description' => 'Minimal tipping-detail polo showcased in the catalog grid.',
                'category_slugs' => ['polo-shirts', 'casual', 'shop'],
            ],
            [
                'name' => 'Black Striped T-shirt',
                'price' => 120,
                'description' => 'Black striped tee featured across catalog and cart with promo price.',
                'category_slugs' => ['t-shirts', 'casual', 'on-sale'],
            ],
            [
                'name' => 'Skinny Fit Jeans',
                'price' => 240,
                'description' => 'Slim-cut denim jeans displayed in the product list section.',
                'category_slugs' => ['jeans', 'casual', 'shop'],
            ],
            [
                'name' => 'Checkered Shirt',
                'price' => 180,
                'description' => 'Checkered shirt from the homepage catalog examples.',
                'category_slugs' => ['shirts', 'casual', 'new-arrivals'],
            ],
            [
                'name' => 'Sleeve Striped T-shirt',
                'price' => 130,
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
                    'compare_price' => $product['compare_price'] ?? null,
                    'description' => $product['description'],
                    'category_id' => $resolveCategoryId($product['category_slugs']),
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
                $user = $users->random();

                $order = Order::create([
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => '0123456789',
                    'address' => 'Seeded Address',
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

    public function createReviews()
    {
        $reviews = [
            [
                'id' => 'r001',
                'productId' => 'p001',
                'ratingStar' => 4.5,
                'isVerified' => true,
                'name' => 'Samantha D.',
                'desc' => 'I absolutely love this t-shirt! The design is unique and the fabric feels so comfortable.',
                'date' => '2023-08-14T00:00:00.000Z',
            ],
            [
                'id' => 'r002',
                'productId' => 'p002',
                'ratingStar' => 4.0,
                'isVerified' => true,
                'name' => 'Alex M.',
                'desc' => 'Good quality polo and true to size. Delivery was fast too.',
                'date' => '2023-08-17T00:00:00.000Z',
            ],
            [
                'id' => 'r003',
                'productId' => 'p003',
                'ratingStar' => 5.0,
                'isVerified' => false,
                'name' => 'Chris W.',
                'desc' => 'Great print, soft material, and color matches the photos.',
                'date' => '2023-08-20T00:00:00.000Z',
            ],
            [
                'id' => 'r004',
                'productId' => 'p004',
                'ratingStar' => 3.5,
                'isVerified' => true,
                'name' => 'Jamie K.',
                'desc' => 'Nice fit overall, but I wish the fabric was a bit thicker.',
                'date' => '2023-08-23T00:00:00.000Z',
            ],
            [
                'id' => 'r005',
                'productId' => 'p005',
                'ratingStar' => 4.5,
                'isVerified' => true,
                'name' => 'Taylor R.',
                'desc' => 'Looks premium and very comfortable for daily wear.',
                'date' => '2023-08-26T00:00:00.000Z',
            ],
        ];

        $products = Product::query()
            ->select(['id', 'slug'])
            ->orderBy('id')
            ->get();

        if ($products->isEmpty() || empty($reviews)) {
            return;
        }

        $productBySlug = $products->keyBy('slug');

        // Frontend product IDs are mapped to known seeded base products.
        $manualProductMap = [
            'p001' => 'one-life-graphic-t-shirt',
            'p002' => 'polo-with-contrast-trims',
            'p003' => 'gradient-graphic-t-shirt',
            'p004' => 'polo-with-tipping-details',
            'p005' => 'black-striped-t-shirt',
            'p006' => 'skinny-fit-jeans',
            'p007' => 'checkered-shirt',
            'p008' => 'sleeve-striped-t-shirt',
        ];

        foreach ($reviews as $review) {
            $resolvedProductId = $this->resolveReviewProductId(
                $review['productId'],
                $products,
                $productBySlug,
                $manualProductMap
            );

            if ($resolvedProductId === null) {
                continue;
            }

            $reviewUser = $this->resolveReviewUser($review['name']);
            $createdAt = Carbon::parse($review['date']);

            $alreadyExists = Review::query()
                ->where('product_id', $resolvedProductId)
                ->where('user_id', $reviewUser->id)
                ->where('comment', $review['desc'])
                ->where('created_at', $createdAt)
                ->exists();

            if ($alreadyExists) {
                continue;
            }

            $createdReview = Review::create([
                'product_id' => $resolvedProductId,
                'user_id' => $reviewUser->id,
                'rating' => $review['ratingStar'],
                'comment' => $review['desc'],
                'is_verified' => $review['isVerified'],
            ]);

            $createdReview->created_at = $createdAt;
            $createdReview->updated_at = $createdAt;
            $createdReview->save();
        }

        $reviewUserNames = [
            'Samantha D.',
            'Alex M.',
            'Ethan R.',
            'Olivia P.',
            'Liam K.',
            'Ava H.',
            'Noah T.',
            'Mia L.',
        ];

        $ratingOptions = [2, 2.5, 3, 3.5, 4, 4.5, 5];
        $comments = [
            "I absolutely love this t-shirt! The design is unique and the fabric feels so comfortable. As a fellow designer, I appreciate the attention to detail. It's become my favorite go-to shirt.",
            "This t-shirt is a must-have for anyone who appreciates good design. The minimalistic yet stylish pattern caught my eye, and the fit is perfect. I can see the designer's touch in every aspect of this shirt.",
            "This t-shirt is a fusion of comfort and creativity. The fabric is soft, and the design speaks volumes about the designer's skill. It's like wearing a piece of art that reflects my passion for both design and fashion.",
            "The t-shirt exceeded my expectations! The colors are vibrant and the print quality is top-notch. Being a UI/UX designer myself, I'm quite picky about aesthetics, and this t-shirt definitely gets a thumbs up from me.",
            "As a UI/UX enthusiast, I value simplicity and functionality. This t-shirt not only represents those principles but also feels great to wear. It's evident that the designer poured their creativity into making this t-shirt stand out.",
            "I'm not just wearing a t-shirt; I'm wearing a piece of design philosophy. The intricate details and thoughtful layout of the design make this shirt a conversation starter."
        ];

        $targetReviewsPerProduct = 30;
        $allProducts = Product::all();

        foreach ($allProducts as $product) {
            $currentCount = Review::where('product_id', $product->id)->count();

            if ($currentCount >= $targetReviewsPerProduct) {
                continue;
            }

            $missingCount = $targetReviewsPerProduct - $currentCount;

            $candidateRows = [];
            foreach ($reviewUserNames as $name) {
                $user = $this->resolveReviewUser($name);

                foreach ($comments as $comment) {
                    $candidateRows[] = [
                        'user' => $user,
                        'comment' => $comment,
                    ];
                }
            }

            shuffle($candidateRows);
            $insertedCount = 0;

            foreach ($candidateRows as $candidate) {
                if ($insertedCount >= $missingCount) {
                    break;
                }

                $user = $candidate['user'];
                $comment = $candidate['comment'];

                $alreadyExists = Review::where('product_id', $product->id)
                    ->where('user_id', $user->id)
                    ->where('comment', $comment)
                    ->exists();

                if ($alreadyExists) {
                    continue;
                }

                $createdAt = now()->subDays(rand(0, 60));

                $review = Review::create([
                    'product_id' => $product->id,
                    'user_id' => $user->id,
                    'rating' => Arr::random($ratingOptions),
                    'comment' => $comment,
                    'is_verified' => rand(1, 100) <= 70,
                ]);

                $review->created_at = $createdAt;
                $review->updated_at = $createdAt;
                $review->save();

                $insertedCount++;
            }
        }
    }

    protected function resolveReviewUser(string $name): User
    {
        $existingUser = User::query()->where('name', $name)->first();

        if ($existingUser) {
            return $existingUser;
        }

        return User::create([
            'name' => $name,
            'email' => $this->generateUniqueReviewUserEmail($name),
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);
    }

    protected function generateUniqueReviewUserEmail(string $name): string
    {
        $base = Str::slug($name, '.');
        $base = $base !== '' ? $base : 'review.user';

        $candidate = "{$base}@example.com";
        $suffix = 1;

        while (User::withTrashed()->where('email', $candidate)->exists()) {
            $candidate = "{$base}.{$suffix}@example.com";
            $suffix++;
        }

        return $candidate;
    }

    protected function resolveReviewProductId(
        string $frontendProductId,
        Collection $products,
        Collection $productBySlug,
        array $manualProductMap
    ): ?int {
        $mappedSlug = $manualProductMap[$frontendProductId] ?? null;

        if ($mappedSlug && $productBySlug->has($mappedSlug)) {
            return $productBySlug->get($mappedSlug)->id;
        }

        if (preg_match('/^p(\d+)$/', $frontendProductId, $matches) === 1) {
            $index = max(0, (int) $matches[1] - 1);
            $product = $products->get($index);

            if ($product) {
                return $product->id;
            }
        }

        return $products->first()?->id;
    }
}
