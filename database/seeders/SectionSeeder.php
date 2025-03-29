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
          ['slug' => 'feed'],
          [
            'title' => 'Feed',
            'slug' => 'feed',
            'type' => 'wire',
            'component' => 'feed',
          ],
        );

        $insights = Section::firstOrCreate(
          ['slug' => 'insights'],
          [
            'title' => 'insights',
            'slug' => 'insights',
            'type' => 'wire',
            'component' => 'insights',
          ],
        );

        $custom = Section::firstOrCreate(
          ['slug' => 'custom'],
          [
            'title' => 'Custom',
            'slug' => 'custom',
            'type' => 'site',
            'component' => 'custom',
          ],
        );

        $helpCenter = Section::firstOrCreate(
          ['slug' => 'help-center'],
          [
            'title' => 'Help Center',
            'slug' => 'help-center',
            'type' => 'site',
            'component' => 'help_center',
          ],
        );
        
        $callbackForm = Section::firstOrCreate(
          ['slug' => 'callback-form'],
          [
            'title' => 'Callback Form',
            'slug' => 'callback-form',
            'type' => 'site',
            'component' => 'callback_form',
          ],
        );
        
        $error_404 = Section::firstOrCreate(
          ['slug' => 'error-404'],
          [
            'title' => 'Error 404',
            'slug' => 'error-404',
            'type' => 'site',
            'component' => '404',
          ],
        );
        
        $auth = Section::firstOrCreate(
          ['slug' => 'auth'],
          [
            'title' => 'Auth',
            'slug' => 'auth',
            'type' => 'wire',
            'component' => 'auth',
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
                'catalog_button_link' => '/advantures',
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
                'header' => 'Travel Insights',
                'article_ids' => [1, 2, 3],
                'more_text' => 'Read Travel Stories',
                'more_link' => '/insights',
              ]
            ],
            [
              'model' => Section::where(['slug' => 'news'])->first(),
              'variables' => [
                'heading' => 'h2',
                'header' => 'Travel News',
                'more_text' => 'Latest Updates',
                'more_link' => '/news',
              ]
            ],
            [
              'model' => Section::where(['slug' => 'popular-products'])->first(),
              'variables' => [
                'heading' => 'h2',
                'header' => 'Trending Products',
                'product_ids' => [1],
                'more_text' => 'Discover Now',
                'more_link' => '/advantures',
                'cart_button_text' => 'Add to cart'
              ]
            ],
            [
              'model' => Section::where(['slug' => 'authors'])->first(),
              'variables' => [
                'heading' => 'h2',
                'header' => 'Featured Creators',
                'author_ids' => [1, 2, 3],
                'more_text' => 'Connect with Creators',
                'more_link' => '/authors',
              ]
            ],
          ]
        ],
        [
          'page' => Page::where('slug', 'feed')->first(),
          'sections' => [
            [
              'model' => Section::where('slug', 'feed')->first(),
              'variables' => [
                'subscribe_heading' => 'h3',
                'subscribe_header' => "Don't Miss Out! Subscribe for Exclusive Content",
                'subscribe_button' => 'Subscribe',
                'profile_subscribe_message' => 'Subscribe',
                'last_news_heading' => 'h3',
                'last_news_header' => 'Travel News',
                'share_message' => 'Share',
                'comment_heading' => 'h2',
                'comment_header' => 'Comments',
                'comment_add_message' => 'Add a comment...',
                'comment_more_message' => 'More',
                'comment_reply_message' => 'Reply',
                'comment_show_replies' => 'Show More Replies',
                'comment_more_comments' => 'Load More Comments',
                'comment_report_message' => 'Report',
                'comment_edit_message' => 'Edit',
                'comment_delete_message' => 'Delete',
                'analog_heading' => 'h2',
                'analog_header' => 'You Might Also Like',
              ],
            ]
          ]
        ],
        [
          'page' => Page::where('slug', 'insights')->first(),
          'sections' => [
            [
              'model' => Section::where('slug', 'insights')->first(),
              'variables' => [
                'heading' => 'h2',
                'header' => 'Travel Insights',
                'search_text' => 'Search by keywords and tags',
                'last_news_heading' => 'h3',
                'last_news_header' => 'Travel News'
              ],
            ],
          ]
        ],
        [
          'page' => Page::where('slug', 'help-center')->first(),
          'sections' => [
            [
              'model' => Section::where('slug', 'help-center')->first(),
              'variables' => [
                'heading' => 'h2',
                'header' => 'Help Center',
                'general_heading' => 'h2',
                'general_header' => 'General Questions:',
                'customer_heading' => 'h2',
                'customer_header' => 'For Customers:',
                'creator_heading' => 'h2',
                'creator_header' => 'For Creators:',
                'last_news_heading' => 'h3',
                'last_news_header' => 'Travel News'
              ],
            ],
            [
              'model' => Section::where('slug', 'callback-form')->first(),
              'variables' => [
                'heading' => 'h2',
                'header' => 'Get in Touch',
                'subtitle' => 'Have a question? We\'re here to assist. Send us a message.',
                'name_placeholder' => 'Your Name',
                'subject_placeholder' => 'Subject...',
                'message_placeholder' => 'Your Message&#10;Please provide details about your request',
                'button_message' => 'Submit',
              ]
            ]
          ]
        ],
        [
          'page' => Page::where('slug', 'error-404')->first(),
          'sections' => [
            [
              'model' => Section::where('slug', 'error-404')->first(),
              'variables' => [
                'heading' => 'h2',
                'header' => 'Oops! Page Not Found',
                'subtitle' => 'If you were looking for something specific, try using the search bar.<br>If you think you\'ve reached this page in error, please get in touch so we can help.',
                'search_text' => 'Search for travel guides, maps, or articles...',
                'product_link' => '/advantures',
                'product_message' => 'All Products',
                'report_link' => '/report',
                'report_message' => 'Report a Problem',
              ]
            ],
          ]
        ],
        [
          'page' => '*',
          'sections' => [
            [
              'model' => Section::where('slug', 'auth')->first(),
              'variables' => [
                'heading' => 'h2',
                'header' => 'Auth',
                'login_button' => 'Login',
                'remember_label' => 'Remember me',
                'email_label' => 'Email',
                'password_label' => 'Password',
              ]
            ]
          ]
        ]
      ];

      foreach ($data as $item) {
        foreach ($item['sections'] as $section) {
          if ($item['page'] != '*') {
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
          }
    
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
