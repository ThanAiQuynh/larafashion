<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\ChatbotConfig;
use App\Models\ChatbotLead;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::updateOrCreate(
            ['email' => 'admin@larafashion.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        $this->command->info('✓ Admin user created');

        // Create Categories
        $categories = [
            'Áo' => ['Áo thun', 'Áo sơ mi', 'Áo khoác', 'Áo len'],
            'Quần' => ['Quần jean', 'Quần kaki', 'Quần short', 'Quần tây'],
            'Váy' => ['Váy ngắn', 'Váy dài', 'Váy công sở'],
            'Phụ kiện' => ['Túi xách', 'Thắt lưng', 'Mũ nón', 'Kính mắt'],
        ];

        foreach ($categories as $parentName => $children) {
            $parent = Category::updateOrCreate(
                ['slug' => Str::slug($parentName)],
                ['name' => $parentName, 'is_active' => true]
            );

            foreach ($children as $childName) {
                Category::updateOrCreate(
                    ['slug' => Str::slug($childName)],
                    ['name' => $childName, 'parent_id' => $parent->id, 'is_active' => true]
                );
            }
        }

        $this->command->info('✓ Categories created');

        // Create Brands
        $brands = ['Nike', 'Adidas', 'Uniqlo', 'Zara', 'H&M', 'Local Brand'];
        foreach ($brands as $brandName) {
            Brand::updateOrCreate(
                ['slug' => Str::slug($brandName)],
                ['name' => $brandName]
            );
        }

        $this->command->info('✓ Brands created');

        // Create Products
        $this->createProducts();
        $this->command->info('✓ Products created');

        // Create Chatbot Config
        ChatbotConfig::updateOrCreate(
            ['id' => 1],
            [
                'script_code' => null,
                'is_active' => false,
                'webhook_secret' => Str::random(32),
            ]
        );

        $this->command->info('✓ Chatbot config created');

        // Create Sample Leads
        $this->createSampleLeads();
        $this->command->info('✓ Sample leads created');

        $this->command->newLine();
        $this->command->info('🎉 Database seeding completed!');
    }

    private function createProducts(): void
    {
        $products = [
            [
                'name' => 'Áo thun basic cotton',
                'category' => 'ao-thun',
                'brand' => 'uniqlo',
                'price' => 299000,
                'original_price' => 399000,
                'description' => 'Áo thun basic chất liệu cotton 100% mềm mại, thoáng mát. Phù hợp mặc hàng ngày.',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=400&h=500&fit=crop',
            ],
            [
                'name' => 'Áo sơ mi trắng công sở',
                'category' => 'ao-so-mi',
                'brand' => 'zara',
                'price' => 650000,
                'description' => 'Áo sơ mi trắng form slim fit, chất liệu cotton pha polyester chống nhăn.',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=400&h=500&fit=crop',
            ],
            [
                'name' => 'Quần jean skinny xanh đậm',
                'category' => 'quan-jean',
                'brand' => 'local-brand',
                'price' => 550000,
                'original_price' => 750000,
                'description' => 'Quần jean skinny fit co giãn thoải mái. Màu xanh đậm classic.',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1542272604-787c3835535d?w=400&h=500&fit=crop',
            ],
            [
                'name' => 'Áo khoác bomber nam',
                'category' => 'ao-khoac',
                'brand' => 'nike',
                'price' => 1200000,
                'description' => 'Áo khoác bomber phong cách streetwear. Chất liệu polyester chống nước nhẹ.',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=400&h=500&fit=crop',
                'is_featured' => true,
            ],
            [
                'name' => 'Váy midi xếp ly',
                'category' => 'vay-dai',
                'brand' => 'hm',
                'price' => 890000,
                'description' => 'Váy midi dáng xòe nhẹ, xếp ly tinh tế. Phù hợp đi làm hoặc dạo phố.',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=400&h=500&fit=crop',
                'is_featured' => true,
            ],
            [
                'name' => 'Túi xách tote da PU',
                'category' => 'tui-xach',
                'brand' => 'zara',
                'price' => 450000,
                'original_price' => 600000,
                'description' => 'Túi xách tote size lớn, chất liệu da PU cao cấp. Nhiều ngăn tiện dụng.',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?w=400&h=500&fit=crop',
            ],
            [
                'name' => 'Quần short kaki nam',
                'category' => 'quan-short',
                'brand' => 'uniqlo',
                'price' => 399000,
                'description' => 'Quần short kaki thoáng mát cho mùa hè. Form regular fit.',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1591195853828-11db59a44f6b?w=400&h=500&fit=crop',
            ],
            [
                'name' => 'Áo len cổ tròn',
                'category' => 'ao-len',
                'brand' => 'hm',
                'price' => 550000,
                'description' => 'Áo len mềm mại ấm áp. Phù hợp thời tiết se lạnh.',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1576871337622-98d48d1cf531?w=400&h=500&fit=crop',
            ],
        ];

        foreach ($products as $index => $productData) {
            $category = Category::where('slug', $productData['category'])->first();
            $brand = Brand::where('slug', $productData['brand'])->first();

            Product::updateOrCreate(
                ['sku' => 'LF-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT)],
                [
                    'name' => $productData['name'],
                    'slug' => Str::slug($productData['name']),
                    'category_id' => $category?->id,
                    'brand_id' => $brand?->id,
                    'price' => $productData['price'],
                    'original_price' => $productData['original_price'] ?? null,
                    'description' => $productData['description'],
                    'thumbnail_url' => $productData['thumbnail_url'],
                    'stock_quantity' => rand(10, 100),
                    'is_active' => true,
                    'is_featured' => $productData['is_featured'] ?? false,
                ]
            );
        }
    }

    private function createSampleLeads(): void
    {
        $leads = [
            [
                'customer_name' => 'Nguyễn Văn An',
                'customer_phone' => '0901234567',
                'intent' => 'Tìm áo thun nam size L màu trắng',
                'is_processed' => false,
            ],
            [
                'customer_name' => 'Trần Thị Bình',
                'customer_phone' => '0912345678',
                'customer_email' => 'binh.tran@email.com',
                'intent' => 'Hỏi về váy công sở size M',
                'is_processed' => true,
            ],
            [
                'customer_name' => 'Lê Hoàng Cường',
                'customer_phone' => '0923456789',
                'intent' => 'Muốn mua quần jean, cần tư vấn size',
                'is_processed' => false,
            ],
        ];

        foreach ($leads as $leadData) {
            ChatbotLead::create([
                ...$leadData,
                'tudongchat_session_id' => 'session_' . Str::random(16),
                'raw_data' => $leadData,
            ]);
        }
    }
}
