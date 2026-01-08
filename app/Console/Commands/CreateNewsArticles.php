<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\User;
use App\Enums\Status;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class CreateNewsArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:create 
                            {--count=10 : Number of news articles to create}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create news articles for Travel News section';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = (int) $this->option('count');

        // Check database connection before proceeding
        try {
            \Illuminate\Support\Facades\DB::connection()->getPdo();
        } catch (\Exception $e) {
            $this->error("Database connection failed: " . $e->getMessage());
            $this->warn("Make sure the database is running and accessible.");
            $this->warn("If using Docker, ensure containers are running: docker-compose up -d");
            $this->warn("Check your .env file DB_HOST setting matches your environment.");
            return Command::FAILURE;
        }

        // Создаем или находим системного пользователя для новостей
        try {
            $newsUser = User::firstOrCreate(
                ['username' => 'trekguider_news'],
                [
                    'name' => 'TrekGuider News',
                    'email' => 'news@trekguider.com',
                    'password' => Hash::make(uniqid('', true)),
                    'email_verified_at' => Carbon::now(),
                ]
            );
        } catch (\Exception $e) {
            $this->error("Failed to create or find news user: " . $e->getMessage());
            return Command::FAILURE;
        }

        $this->info("✓ News user ready: {$newsUser->username} (ID: {$newsUser->id})");

        // Массив примеров новостей о путешествиях
        $newsTitles = [
            'Top 10 Hidden Gems in Southeast Asia',
            'Sustainable Travel: How to Reduce Your Carbon Footprint',
            'Best Time to Visit European Capitals',
            'Solo Travel Safety Tips for 2025',
            'Budget-Friendly Destinations for Adventure Seekers',
            'Cultural Etiquette: What Every Traveler Should Know',
            'Travel Insurance Guide: What You Need to Know',
            'Digital Nomad Destinations: Best Cities for Remote Work',
            'Family-Friendly Travel: Planning the Perfect Trip',
            'Off-the-Beaten-Path Destinations in South America',
            'Travel Photography Tips: Capturing Memories',
            'Eco-Friendly Hotels: Sustainable Accommodation Options',
            'Travel Hacks: Save Money on Your Next Adventure',
            'Festival Travel: Best Cultural Events Around the World',
            'Adventure Travel: Extreme Sports Destinations',
        ];

        $newsContent = [
            '<h3>Discover Hidden Treasures</h3>
            <p>Explore the most beautiful and less-known destinations that offer authentic experiences away from tourist crowds. These hidden gems provide unique cultural insights and breathtaking landscapes.</p>
            <h4>Why Visit Off-the-Beaten-Path Locations?</h4>
            <p>Traveling to lesser-known destinations allows you to experience local culture more authentically, support local economies, and create unforgettable memories. You\'ll discover pristine natural beauty and connect with communities in meaningful ways.</p>
            <p>Whether you\'re seeking adventure, relaxation, or cultural immersion, these destinations offer something special for every type of traveler.</p>',

            '<h3>Travel Responsibly</h3>
            <p>Sustainable travel is becoming increasingly important as we become more aware of our environmental impact. Learn how to make your travels more eco-friendly while still enjoying amazing experiences.</p>
            <h4>Simple Steps to Travel Sustainably</h4>
            <p>Choose eco-friendly accommodations, use public transportation, support local businesses, and minimize your waste. Every small action contributes to preserving destinations for future generations.</p>
            <p>By making conscious choices, you can explore the world while protecting it for others to enjoy.</p>',

            '<h3>Plan Your Perfect European Adventure</h3>
            <p>Europe offers diverse experiences throughout the year. Each season brings unique opportunities, from summer festivals to winter wonderlands. Timing your visit can make all the difference.</p>
            <h4>Seasonal Highlights</h4>
            <p>Spring brings blooming flowers and mild weather, perfect for city exploration. Summer offers long days and vibrant festivals. Autumn provides stunning foliage and fewer crowds. Winter transforms cities into magical wonderlands with Christmas markets.</p>
            <p>Research your destination\'s peak seasons and local events to maximize your European adventure.</p>',

            '<h3>Stay Safe While Exploring Solo</h3>
            <p>Solo travel can be incredibly rewarding, offering freedom and self-discovery. However, safety should always be your top priority when traveling alone.</p>
            <h4>Essential Safety Tips</h4>
            <p>Share your itinerary with trusted contacts, stay aware of your surroundings, trust your instincts, and keep important documents secure. Research your destination\'s safety situation and local customs before you go.</p>
            <p>With proper preparation, solo travel can be one of the most empowering experiences of your life.</p>',

            '<h3>Adventure on a Budget</h3>
            <p>You don\'t need to break the bank to have amazing adventures. Many incredible destinations offer affordable travel options for budget-conscious explorers.</p>
            <h4>Budget Travel Strategies</h4>
            <p>Look for off-season deals, consider alternative accommodations like hostels or homestays, cook some meals yourself, and use local transportation. Many of the best travel experiences are free or low-cost.</p>
            <p>With smart planning, you can explore the world without emptying your savings account.</p>',
        ];

        $created = 0;
        $existing = 0;

        for ($i = 0; $i < $count; $i++) {
            $titleIndex = $i % count($newsTitles);
            $contentIndex = $i % count($newsContent);
            
            $title = $newsTitles[$titleIndex];
            if ($i >= count($newsTitles)) {
                $title = $newsTitles[$titleIndex] . ' ' . ($i + 1);
            }

            $article = Article::firstOrCreate(
                [
                    'user_id' => $newsUser->id,
                    'title' => $title,
                ],
                [
                    'user_id' => $newsUser->id,
                    'title' => $title,
                    'text' => $newsContent[$contentIndex],
                    'status_id' => Status::ACTIVE,
                    'views' => rand(50, 500),
                    'published_at' => Carbon::now()->subDays(rand(0, 30)),
                ]
            );

            if ($article->wasRecentlyCreated) {
                $created++;
                $this->line("✓ Created: {$title}");
            } else {
                $existing++;
            }
        }

        $this->newLine();
        $this->info("✓ News articles creation complete!");
        $this->info("Created: {$created} new articles");
        if ($existing > 0) {
            $this->line("Skipped: {$existing} existing articles");
        }

        return Command::SUCCESS;
    }
}

