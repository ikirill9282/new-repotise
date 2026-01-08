<?php

namespace Database\Seeders;

use App\Enums\Status;
use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Gallery;
use App\Models\Location;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\Tag;
use App\Models\Type;
use App\Models\User;
use App\Models\UserFavorite;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RealisticDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌍 Начинаем заполнение сайта реалистичными данными...');

        // Получаем роли
        $creatorRole = Role::where('name', 'creator')->first();
        $customerRole = Role::where('name', 'customer')->first();
        
        if (!$creatorRole || !$customerRole) {
            $this->command->error('Роли не найдены. Запустите RoleSeeder сначала.');
            return;
        }

        // 1. Создаем пользователей-создателей (продавцов)
        $this->command->info('👥 Создаем пользователей-создателей...');
        $creators = $this->createCreators($creatorRole, 30);
        
        // 2. Создаем обычных пользователей (покупателей)
        $this->command->info('👤 Создаем обычных пользователей...');
        $customers = $this->createCustomers($customerRole, 50);
        
        // 3. Создаем товары
        $this->command->info('📦 Создаем товары...');
        $products = $this->createProducts($creators, 150);
        
        // 4. Создаем статьи от создателей
        $this->command->info('📝 Создаем статьи...');
        $articles = $this->createArticles($creators, 80);
        
        // 5. Создаем новости (от системного пользователя)
        $this->command->info('📰 Создаем новости...');
        $news = $this->createNews(20);
        
        // 6. Создаем отзывы на товары
        $this->command->info('⭐ Создаем отзывы...');
        $this->createReviews($products, $customers, 200);
        
        // 7. Создаем комментарии к статьям
        $this->command->info('💬 Создаем комментарии...');
        $this->createComments($articles, $customers, 150);
        
        // 8. Создаем лайки
        $this->command->info('❤️ Создаем лайки...');
        $this->createLikes($articles, $customers, 300);
        
        // 9. Создаем подписки (followers)
        $this->command->info('👥 Создаем подписки...');
        $this->createFollowers($creators, $customers, 200);
        
        // 10. Создаем избранное
        $this->command->info('⭐ Создаем избранное...');
        $this->createFavorites($products, $customers, 150);
        
        // 11. Создаем заказы
        $this->command->info('🛒 Создаем заказы...');
        $this->createOrders($products, $customers, 80);
        
        // 12. Обновляем счетчики
        $this->command->info('📊 Обновляем счетчики...');
        $this->updateCounters();

        $this->command->info('✅ Заполнение сайта завершено!');
        $this->command->info("📈 Создано:");
        $this->command->info("   - Пользователей-создателей: " . count($creators));
        $this->command->info("   - Обычных пользователей: " . count($customers));
        $this->command->info("   - Товаров: " . count($products));
        $this->command->info("   - Статей: " . count($articles));
        $this->command->info("   - Новостей: " . count($news));
    }

    protected function createCreators($role, int $count): array
    {
        $travelNames = [
            'Mountain Explorer', 'Ocean Wanderer', 'Desert Nomad', 'Forest Guide', 
            'City Traveler', 'Adventure Seeker', 'Wildlife Photographer', 'Cultural Explorer',
            'Backpacker Pro', 'Luxury Traveler', 'Budget Explorer', 'Solo Wanderer',
            'Family Travel Guide', 'Extreme Adventurer', 'Nature Lover', 'History Buff',
            'Food Traveler', 'Beach Comber', 'Mountain Climber', 'River Rafting Guide',
            'Safari Expert', 'Arctic Explorer', 'Tropical Paradise', 'Ancient Cities',
            'Hidden Gems', 'Travel Stories', 'Journey Planner', 'Destination Expert',
            'Travel Photographer', 'Adventure Guide'
        ];

        $creators = [];
        for ($i = 0; $i < $count; $i++) {
            $name = $travelNames[$i % count($travelNames)] . ($i >= count($travelNames) ? ' ' . ($i + 1) : '');
            $baseUsername = str()->slug($name);
            $username = $baseUsername . rand(1000, 9999);
            
            $user = User::withoutEvents(function () use ($name, $username, $role) {
                $user = User::firstOrCreate(
                    ['username' => $username],
                    [
                        'name' => $name,
                        'email' => str()->slug($username) . '@trekguider.com',
                        'email_verified_at' => Carbon::now()->subDays(rand(1, 365)),
                        'password' => bcrypt('password'),
                        'verified' => rand(0, 1),
                        'active' => 1,
                        'balance' => rand(0, 5000) / 100,
                    ]
                );
                
                if (!$user->hasRole($role->name)) {
                    $user->assignRole($role);
                }
                
                // Создаем опции пользователя если их нет
                if (!$user->options) {
                    $firstLevel = \App\Models\Level::first();
                    $user->options()->create([
                        'description' => fake()->paragraph(3),
                        'avatar' => '/storage/images/default_avatar.png',
                        'level_id' => $firstLevel?->id ?? null,
                        'notification_settings' => [
                            'product_updates' => true,
                            'referral_updates' => true,
                            'news_updates' => true,
                        ],
                    ]);
                } elseif (empty($user->options->avatar)) {
                    // Обновляем аватар если его нет
                    $user->options->update(['avatar' => '/storage/images/default_avatar.png']);
                }
                
                return $user;
            });
            
            $creators[] = $user;
        }

        return $creators;
    }

    protected function createCustomers($role, int $count): array
    {
        $customers = [];
        for ($i = 0; $i < $count; $i++) {
            $user = User::withoutEvents(function () use ($role) {
                $username = fake()->unique()->userName() . rand(1000, 9999);
                $email = fake()->unique()->safeEmail();
                
                $user = User::firstOrCreate(
                    ['username' => $username],
                    [
                        'name' => fake()->name(),
                        'email' => $email,
                        'email_verified_at' => Carbon::now()->subDays(rand(1, 180)),
                        'password' => bcrypt('password'),
                        'verified' => rand(0, 1),
                        'active' => 1,
                        'balance' => rand(0, 1000) / 100,
                    ]
                );
                
                if (!$user->hasRole($role->name)) {
                    $user->assignRole($role);
                }
                
                if (!$user->options) {
                    $firstLevel = \App\Models\Level::first();
                    $user->options()->create([
                        'description' => null,
                        'avatar' => '/storage/images/default_avatar.png',
                        'level_id' => $firstLevel?->id ?? null,
                        'notification_settings' => [
                            'product_updates' => true,
                            'referral_updates' => true,
                            'news_updates' => true,
                        ],
                    ]);
                } elseif (empty($user->options->avatar)) {
                    // Обновляем аватар если его нет
                    $user->options->update(['avatar' => '/storage/images/default_avatar.png']);
                }
                
                return $user;
            });
            
            $customers[] = $user;
        }

        return $customers;
    }

    protected function createProducts(array $creators, int $count): array
    {
        $productTitles = [
            'Complete Guide to Hiking in the Alps',
            'Best Beaches in Southeast Asia',
            'Safari Adventure in Kenya',
            'Budget Travel Guide: Europe',
            'Luxury Hotels in Maldives',
            'Backpacking Route: South America',
            'Photography Spots in Iceland',
            'Food Tour: Tokyo Street Food',
            'Mountain Climbing: Everest Base Camp',
            'Cultural Tour: Ancient Egypt',
            'Road Trip: Pacific Coast Highway',
            'Diving Spots: Great Barrier Reef',
            'Desert Adventure: Sahara Expedition',
            'City Guide: New York on a Budget',
            'Tropical Paradise: Bali Travel Guide',
            'Northern Lights: Best Viewing Spots',
            'Wine Tour: Tuscany, Italy',
            'Adventure Sports: New Zealand',
            'Historical Sites: Rome Walking Tour',
            'Wildlife Watching: Galapagos Islands',
        ];

        $descriptions = [
            'Discover the most beautiful hiking trails with detailed maps and tips.',
            'Your complete guide to planning the perfect beach vacation.',
            'Experience wildlife like never before with our comprehensive safari guide.',
            'Travel through Europe without breaking the bank with our budget tips.',
            'Indulge in luxury at the world\'s most beautiful resorts.',
            'Follow this route for an unforgettable backpacking adventure.',
            'Capture stunning photos at these breathtaking locations.',
            'Explore the vibrant street food scene in one of Asia\'s culinary capitals.',
            'Prepare for the adventure of a lifetime with our detailed guide.',
            'Immerse yourself in ancient history and culture.',
            'Experience one of the world\'s most scenic drives.',
            'Dive into crystal clear waters and discover marine life.',
            'Embark on an epic journey through the world\'s largest desert.',
            'Explore the Big Apple without spending a fortune.',
            'Find your perfect tropical getaway with our comprehensive guide.',
            'Witness one of nature\'s most spectacular light shows.',
            'Savor the finest wines and cuisine in the Italian countryside.',
            'Get your adrenaline pumping with extreme sports adventures.',
            'Walk through history in the Eternal City.',
            'Observe unique wildlife found nowhere else on Earth.',
        ];

        $products = [];
        $categories = Category::all();
        $types = Type::all();
        $locations = Location::all();

        for ($i = 0; $i < $count; $i++) {
            $title = $productTitles[$i % count($productTitles)] . ($i >= count($productTitles) ? ' ' . ($i + 1) : '');
            $creator = $creators[array_rand($creators)];
            $price = rand(20, 200);
            $salePrice = rand(0, 1) ? rand(5, $price - 10) : null;
            $publishedAt = Carbon::now()->subDays(rand(1, 180));

            $product = Product::create([
                'user_id' => $creator->id,
                'title' => $title,
                'price' => $price,
                'sale_price' => $salePrice,
                'subscription' => rand(0, 1),
                'text' => $descriptions[$i % count($descriptions)] . "\n\n" . fake()->paragraphs(rand(5, 10), true),
                'status_id' => Status::ACTIVE,
                'published_at' => $publishedAt,
                'rating' => round(rand(30, 50) / 10, 1),
                'views' => rand(50, 5000),
                'seo_title' => $title,
                'seo_text' => fake()->sentence(),
            ]);

            // Привязываем категории, типы и локации
            if ($categories->isNotEmpty()) {
                $product->categories()->sync($categories->random(rand(1, 3))->pluck('id'));
            }
            if ($types->isNotEmpty()) {
                $product->types()->sync($types->random(rand(1, 2))->pluck('id'));
            }
            if ($locations->isNotEmpty()) {
                $product->locations()->sync($locations->random(rand(1, 3))->pluck('id'));
            }

            // Создаем подписку если нужно
            if ($product->subscription) {
                $product->subprice()->updateOrCreate(
                    ['product_id' => $product->id],
                    [
                        'month' => $price,
                        'quarter' => round($price * 2.5),
                        'year' => round($price * 9),
                    ]
                );
            }

            // Создаем превью изображение для товара
            $productImages = [
                '/storage/images/product_5.webp',
                '/storage/images/product_8.webp',
                '/storage/images/product_9.webp',
                '/storage/images/default_avatar.png',
            ];
            $randomImage = $productImages[array_rand($productImages)];
            $product->gallery()->firstOrCreate([
                'model_id' => $product->id,
                'type' => 'products',
                'preview' => 1,
            ], [
                'image' => $randomImage,
                'user_id' => $product->user_id,
                'size' => 0,
            ]);

            $products[] = $product;
        }

        return $products;
    }

    protected function createArticles(array $creators, int $count): array
    {
        $articleTitles = [
            '10 Must-Visit Destinations in 2024',
            'How to Travel on a Budget: Complete Guide',
            'Best Time to Visit Popular Destinations',
            'Solo Travel: Tips and Safety Guide',
            'Photography Tips for Travelers',
            'Sustainable Travel: How to Be a Responsible Tourist',
            'Hidden Gems: Off-the-Beaten-Path Destinations',
            'Travel Insurance: What You Need to Know',
            'Packing Tips: What to Bring on Your Journey',
            'Cultural Etiquette: Do\'s and Don\'ts',
            'Adventure Travel: Best Destinations for Thrill Seekers',
            'Family Travel: Planning the Perfect Trip',
            'Digital Nomad Lifestyle: Working While Traveling',
            'Food Tourism: Culinary Adventures Around the World',
            'Eco-Friendly Travel: Reducing Your Carbon Footprint',
        ];

        $articles = [];
        for ($i = 0; $i < $count; $i++) {
            $title = $articleTitles[$i % count($articleTitles)] . ($i >= count($articleTitles) ? ' ' . ($i + 1) : '');
            $creator = $creators[array_rand($creators)];
            $publishedAt = Carbon::now()->subDays(rand(1, 120));

            $article = Article::create([
                'user_id' => $creator->id,
                'title' => $title,
                'text' => '<h2>' . $title . '</h2>' . 
                         '<p>' . fake()->paragraphs(rand(8, 15), true) . '</p>' .
                         '<h3>Key Points</h3>' .
                         '<ul><li>' . implode('</li><li>', fake()->sentences(rand(5, 10))) . '</li></ul>' .
                         '<p>' . fake()->paragraphs(rand(3, 6), true) . '</p>',
                'status_id' => Status::ACTIVE,
                'published_at' => $publishedAt,
                'views' => rand(100, 5000),
                'category_id' => rand(0, 1) ? Category::inRandomOrder()->first()?->id : null,
            ]);

            // Привязываем теги
            $tags = Tag::inRandomOrder()->limit(rand(2, 5))->get();
            if ($tags->isNotEmpty()) {
                $article->tags()->sync($tags->pluck('id'));
            }

            // Создаем превью изображение
            $article->gallery()->firstOrCreate([
                'model_id' => $article->id,
                'type' => 'articles',
                'preview' => 1,
            ], [
                'image' => '/storage/images/img_articles.png',
                'user_id' => $article->user_id,
                'size' => 0,
            ]);

            $articles[] = $article;
        }

        return $articles;
    }

    protected function createNews(int $count): array
    {
        $systemUser = User::find(0);
        if (!$systemUser) {
            return [];
        }

        $newsTitles = [
            'New Travel Restrictions Lifted in Multiple Countries',
            'Best Travel Destinations for Spring 2024',
            'Airline Announces New Routes to Popular Destinations',
            'Travel Technology: New Apps for Modern Travelers',
            'Sustainable Tourism Initiatives Gain Momentum',
            'World Heritage Sites: New Additions Announced',
            'Travel Safety: Important Updates for Tourists',
            'Festival Season: Must-Attend Events Around the World',
            'Travel Deals: Best Offers This Month',
            'Cultural Events: What\'s Happening This Season',
        ];

        $news = [];
        for ($i = 0; $i < $count; $i++) {
            $title = $newsTitles[$i % count($newsTitles)] . ($i >= count($newsTitles) ? ' ' . ($i + 1) : '');
            $publishedAt = Carbon::now()->subDays(rand(1, 30));

            $article = Article::create([
                'user_id' => $systemUser->id,
                'title' => $title,
                'text' => '<h2>' . $title . '</h2>' . 
                         '<p>' . fake()->paragraphs(rand(5, 10), true) . '</p>',
                'status_id' => Status::ACTIVE,
                'published_at' => $publishedAt,
                'views' => rand(200, 3000),
            ]);

            // Создаем превью изображение для новости
            $article->gallery()->firstOrCreate([
                'model_id' => $article->id,
                'type' => 'articles',
                'preview' => 1,
            ], [
                'image' => '/storage/images/img_articles.png',
                'user_id' => $article->user_id,
                'size' => 0,
            ]);

            $news[] = $article;
        }

        return $news;
    }

    protected function createReviews(array $products, array $customers, int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            $product = $products[array_rand($products)];
            $customer = $customers[array_rand($customers)];
            $rating = rand(3, 5);

            Review::create([
                'user_id' => $customer->id,
                'product_id' => $product->id,
                'rating' => $rating,
                'text' => fake()->paragraphs(rand(1, 3), true),
                'status_id' => Status::ACTIVE,
                'created_at' => Carbon::now()->subDays(rand(1, 90)),
            ]);
        }
    }

    protected function createComments(array $articles, array $customers, int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            $article = $articles[array_rand($articles)];
            $customer = $customers[array_rand($customers)];

            Comment::create([
                'user_id' => $customer->id,
                'article_id' => $article->id,
                'text' => fake()->paragraph(),
                'status_id' => Status::ACTIVE,
                'created_at' => Carbon::now()->subDays(rand(1, 60)),
            ]);
        }
    }

    protected function createLikes(array $articles, array $customers, int $count): void
    {
        $liked = [];
        for ($i = 0; $i < $count; $i++) {
            $article = $articles[array_rand($articles)];
            $customer = $customers[array_rand($customers)];
            $key = $article->id . '_' . $customer->id;

            if (!isset($liked[$key])) {
                DB::table('likes')->insert([
                    'user_id' => $customer->id,
                    'model_id' => $article->id,
                    'type' => 'article',
                    'created_at' => Carbon::now()->subDays(rand(1, 30)),
                    'updated_at' => Carbon::now()->subDays(rand(1, 30)),
                ]);
                $liked[$key] = true;
            }
        }
    }

    protected function createFollowers(array $creators, array $customers, int $count): void
    {
        $followed = [];
        for ($i = 0; $i < $count; $i++) {
            $creator = $creators[array_rand($creators)];
            $customer = $customers[array_rand($customers)];
            $key = $creator->id . '_' . $customer->id;

            if ($creator->id !== $customer->id && !isset($followed[$key])) {
                DB::table('followers')->insert([
                    'author_id' => $creator->id,
                    'subscriber_id' => $customer->id,
                    'created_at' => Carbon::now()->subDays(rand(1, 180)),
                    'updated_at' => Carbon::now()->subDays(rand(1, 180)),
                ]);
                $followed[$key] = true;
            }
        }
    }

    protected function createFavorites(array $products, array $customers, int $count): void
    {
        $favorited = [];
        for ($i = 0; $i < $count; $i++) {
            $product = $products[array_rand($products)];
            $customer = $customers[array_rand($customers)];
            $key = $product->id . '_' . $customer->id;

            if (!isset($favorited[$key])) {
                UserFavorite::create([
                    'user_id' => $customer->id,
                    'type' => 'product',
                    'item_id' => $product->id,
                    'created_at' => Carbon::now()->subDays(rand(1, 90)),
                ]);
                $favorited[$key] = true;
            }
        }
    }

    protected function createOrders(array $products, array $customers, int $count): void
    {
        $statuses = [\App\Enums\Order::NEW, \App\Enums\Order::PAID, \App\Enums\Order::COMPLETE];
        
        for ($i = 0; $i < $count; $i++) {
            $customer = $customers[array_rand($customers)];
            $product = $products[array_rand($products)];
            $status = $statuses[array_rand($statuses)];
            $price = $product->sale_price ?? $product->price;
            $createdAt = Carbon::now()->subDays(rand(1, 120));

            $order = Order::create([
                'user_id' => $customer->id,
                'status_id' => $status,
                'cost' => $price,
                'cost_without_discount' => $product->price,
                'cost_without_tax' => $price,
                'tax' => round($price * 0.1, 2),
                'discount_amount' => $product->sale_price ? ($product->price - $product->sale_price) : 0,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            $orderProductData = [
                'order_id' => $order->id,
                'product_id' => $product->id,
                'price' => $price,
                'count' => 1,
                'total' => $price,
                'total_without_discount' => $product->price,
            ];
            
            if ($product->sale_price) {
                $orderProductData['sale_price'] = $product->sale_price;
                $orderProductData['discount'] = $product->price - $product->sale_price;
            }
            
            DB::table('order_products')->insert($orderProductData);
        }
    }

    protected function updateCounters(): void
    {
        // Обновляем счетчики подписчиков (если поле существует)
        if (DB::getSchemaBuilder()->hasColumn('users', 'followers_count')) {
            DB::statement('
                UPDATE users 
                SET followers_count = (
                    SELECT COUNT(*) 
                    FROM followers 
                    WHERE followers.author_id = users.id
                )
            ');
        }

        // Обновляем счетчики лайков для статей (если поле существует)
        if (DB::getSchemaBuilder()->hasColumn('articles', 'likes_count')) {
            DB::statement('
                UPDATE articles 
                SET likes_count = (
                    SELECT COUNT(*) 
                    FROM likes 
                    WHERE likes.model_id = articles.id AND likes.type = "article"
                )
            ');
        }

        // Обновляем рейтинги товаров на основе отзывов
        DB::statement('
            UPDATE products 
            SET rating = (
                SELECT COALESCE(AVG(rating), 0)
                FROM reviews 
                WHERE reviews.product_id = products.id 
                AND reviews.status_id = 1
                AND reviews.parent_id IS NULL
            )
        ');
    }
}

