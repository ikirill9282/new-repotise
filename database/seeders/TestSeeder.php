<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Status;
use Spatie\Permission\Models\Role;
use App\Models\Category;
use App\Models\Type;
use App\Models\Location;
use App\Models\Tag;
use App\Models\Order;
use App\Enums\Order as OrderEnum;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds for testing environment.
     */
    public function run(): void
    {
        // Seed Statuses
        $statuses = [
            ['title' => 'Active', 'id' => 1],
            ['title' => 'Draft', 'id' => 2],
            ['title' => 'Pending Review', 'id' => 3],
            ['title' => 'Needs Revision', 'id' => 4],
            ['title' => 'Reject', 'id' => 5],
            ['title' => 'Scheduled', 'id' => 6],
            ['title' => 'Deleted', 'id' => 7],
        ];
        
        foreach ($statuses as $status) {
            Status::firstOrCreate(['id' => $status['id']], $status);
        }

        // Order statuses are stored in status_id field using Status model, not OrderStatus
        // Order enum constants: NEW=1, PAID=2, REWARDING=3, COMPLETE=4
        // These are already covered by Status seeder above

        // Seed Roles
        $roles = [
            ['name' => 'system', 'title' => 'System'],
            ['name' => 'customer', 'title' => 'Customer'],
            ['name' => 'creator', 'title' => 'Creator'],
            ['name' => 'refered-seller', 'title' => 'Refered Seller'],
            ['name' => 'admin', 'title' => 'Admin'],
            ['name' => 'moderator', 'title' => 'Moderator'],
            ['name' => 'super-admin', 'title' => 'Super Admin'],
        ];
        
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }

        // Seed Categories (multiple for tests)
        $categories = [
            ['title' => 'Test Category', 'slug' => 'test-category', 'status_id' => 1],
            ['title' => 'Travel Guides', 'slug' => 'travel-guides', 'status_id' => 1],
            ['title' => 'Adventure', 'slug' => 'adventure', 'status_id' => 1],
        ];
        
        foreach ($categories as $category) {
            Category::firstOrCreate(['slug' => $category['slug']], $category);
        }

        // Seed Types (multiple for tests)
        $types = [
            ['title' => 'Test Type', 'slug' => 'test-type'],
            ['title' => 'Guide', 'slug' => 'guide'],
            ['title' => 'E-book', 'slug' => 'e-book'],
        ];
        
        foreach ($types as $type) {
            Type::firstOrCreate(['slug' => $type['slug']], $type);
        }

        // Seed Locations (multiple for tests)
        $locations = [
            ['title' => 'Test Location', 'slug' => 'test-location'],
            ['title' => 'Europe', 'slug' => 'europe'],
            ['title' => 'Asia', 'slug' => 'asia'],
            ['title' => 'North America', 'slug' => 'north-america'],
        ];
        
        foreach ($locations as $location) {
            Location::firstOrCreate(['slug' => $location['slug']], $location);
        }

        // Seed Tags (for articles and products)
        $tags = [
            ['title' => 'travel', 'slug' => 'travel'],
            ['title' => 'adventure', 'slug' => 'adventure'],
            ['title' => 'guide', 'slug' => 'guide'],
            ['title' => 'tips', 'slug' => 'tips'],
        ];
        
        foreach ($tags as $tag) {
            Tag::firstOrCreate(['slug' => $tag['slug']], $tag);
        }
    }
}
