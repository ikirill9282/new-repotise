<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin\Section;
use App\Models\Admin\Page;
use App\Models\Admin\PageSection;
use App\Models\Admin\SectionVariables;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hero = Section::firstOrCreate(
          ['slug' => 'home'],
          [
            'title' => 'home', 
            'slug' => 'home', 
            'type' => 'site', 
            'component' => 'home'
          ],
        );
        $main_article = Section::firstOrCreate(
          ['slug' => 'main-article'],
          [
            'title' => 'main-article',
            'slug' => 'main-article',
            'type' => 'site',
            'component' => 'main_article',
          ]
        );
        $main_articles = Section::firstOrCreate(
          ['slug' => 'main-articles'],
          [
            'title' => 'main-articles',
            'slug' => 'main-articles',
            'type' => 'site',
            'component' => 'articles',
          ]
        );
        $news = Section::firstOrCreate(
          ['slug' => 'news'],
          [
            'title' => 'news',
            'slug' => 'news',
            'type' => 'site',
            'component' => 'news',
          ]
        );
        $popular_products = Section::firstOrCreate(
          ['slug' => 'popular-products'],
          [
            'title' => 'popular-products',
            'slug' => 'popular-products',
            'type' => 'site',
            'component' => 'popular_products',
          ]
        );
        $authors = Section::firstOrCreate(
          ['slug' => 'authors'],
          [
            'title' => 'authors',
            'slug' => 'authors',
            'type' => 'site',
            'component' => 'authors',
          ]
        );

        $article_feed = Section::firstOrCreate(
          ['slug' => 'insights'],
          [
            'title' => 'insights',
            'slug' => 'insights',
            'type' => 'wire',
            'component' => 'insights',
          ],
        );

        $articles = Section::firstOrCreate(
          ['slug' => 'articles'],
          [
            'title' => 'articles',
            'slug' => 'articles',
            'type' => 'wire',
            'component' => 'articles',
          ],
        );

        $this->build();
    }

    protected function build()
    {
      $data = [
        [
          'page' => Page::where('slug', 'home')->first(),
          'sections' => [
            [
              'model' => Section::where(['slug' => 'home'])->first(),
              'variables' => [
                'heading' => 'h1',
                'header' => 'TrekGuider <span>— Travel Content, Reimagined.</span>',
                'subtitle' => 'Join a global community of travel creators and explorers. Unlock new revenue streams by sharing your knowledge and discover hidden gems. Your adventure, your expertise - all in one place.',
                'catalog_button_text' => 'Explore Your Adventure',
                'catalog_button_link' => '/catalog',
                'catalog_register_text' => 'Become a Creator',
                'catalog_register_link' => '#',
              ]
            ],
            [
              'model' => Section::where(['slug' => 'main-article'])->first(),
              'variables' => [
                'heading' => 'h2',
                'article_id' => 1,
              ]
            ],
            [
              'model' => Section::where(['slug' => 'main-articles'])->first(),
              'variables' => [
                'heading' => 'h2',
                'header' => 'Статьи',
                'article_ids' => [1, 2, 3],
                'more_text' => 'Смотреть все',
                'more_link' => '/articles',
              ]
            ],
            [
              'model' => Section::where(['slug' => 'news'])->first(),
              'variables' => [
                'heading' => 'h2',
                'header' => 'Новости',
                'news_ids' => [1],
                'more_text' => 'Смотреть все',
                'more_link' => '/news',
              ]
            ],
            [
              'model' => Section::where(['slug' => 'popular-products'])->first(),
              'variables' => [
                'heading' => 'h2',
                'header' => 'Популярные товары',
                'product_ids' => [1],
                'more_text' => 'Смотреть все товары',
                'more_link' => '/products',
                'cart_button_text' => 'Add to cart'
              ]
            ],
            [
              'model' => Section::where(['slug' => 'authors'])->first(),
              'variables' => [
                'heading' => 'h2',
                'header' => 'Авторы и блогеры',
                'author_ids' => [1],
                'more_text' => 'Смотреть всех авторов',
                'more_link' => '/authors',
              ]
            ],
          ]
        ],
        [
          'page' => Page::where('slug', 'insights')->first(),
          'sections' => [
            [
              'model' => Section::where('slug', 'insights')->first(),
              'variables' => [
                'subscribe_heading' => 'h3',
                'subscribe_message' => "Don't Miss Out!<br> Subscribe for Exclusive Content",
                'subscribe_button' => 'Subscribe',
                'last_news_heading' => 'h3',
                'last_news_title' => 'Travel News'
              ],
            ]
          ]
        ],
        [
          'page' => Page::where('slug', 'articles')->first(),
          'sections' => [
            [
              'model' => Section::where('slug', 'articles')->first(),
              'variables' => [
                'heading' => 'h2',
                'header' => 'Travel Insights',
                'search_text' => 'Search by keywords and tags',
                'last_news_heading' => 'h3',
                'last_news_title' => 'Travel News'
              ],
            ],
          ]
        ]
      ];

      foreach ($data as $item) {
        foreach ($item['sections'] as $section) {
          PageSection::firstOrCreate(
            [
              'page_id' => $item['page']->id,
              'section_id' => $section['model']->id,
            ],
            [
              'page_id' => $item['page']->id,
              'section_id' => $section['model']->id,
            ]
          );
    
          foreach ($section['variables'] as $name => $value) {
            SectionVariables::firstOrCreate([
              'section_id' => $section['model']->id,
              'name' => $name,
              'value' => (is_array($value)) ? json_encode($value) : $value,
            ]);
          }
        }
      }
    }
}
