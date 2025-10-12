<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
      $this->call([
        RoleSeeder::class, 
        LevelSeeder::class,
        UserSeeder::class,
        StatusSeeder::class,
        
        PageSeeder::class,
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
        PagesConfigSeeder::class,
        UserOptionSeeder::class,
        CountrySeeder::class,
        LanguageSeeder::class,
        PolicySeeder::class,

        MeilisearchSeeder::class,
      ]);
    }
}
