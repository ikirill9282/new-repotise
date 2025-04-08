<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\ArticleLikes;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Meilisearch\Meilisearch;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
      $this->call([
        RoleSeeder::class, 
        UserSeeder::class,
        PageSeeder::class,
        SectionSeeder::class,
        TypeSeeder::class,
        LocationSeeder::class,
        CategorySeeder::class,
        ArticleSeeder::class,
        ProductSeeder::class,
        ReviewSeeder::class,
        TagSeeder::class,
        CommentSeeder::class,
        LikeSeeder::class,
        FollowerSeeder::class,
        GallerySeeder::class,
        FaqSeeder::class,
        OptionSeeder::class,

        MeilisearchSeeder::class,
      ]);
    }
}
