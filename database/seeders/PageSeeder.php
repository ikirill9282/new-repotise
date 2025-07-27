<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Page;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      $main_page = Page::firstOrCreate([
        'title' => 'Home',
        'slug' => 'home',
      ]);
      $articles_page = Page::firstOrCreate([
        'title' => 'Travel Insights',
        'slug' => 'insights',
      ]);
      // $news_page = Page::firstOrCreate([
      //   'title' => 'news',
      //   'slug' => 'news',
      // ]);
      $feed_page = Page::firstOrCreate([
        'title' => 'Feed',
        'slug' => 'feed',
      ]);
      $faq = Page::firstOrCreate([
        'title' => 'Help Center',
        'slug' => 'help-center',
      ]);
      $error_404 = Page::firstOrCreate([
        'title' => '404',
        'slug' => '404',
      ]);
      $search = Page::firstOrCreate([
        'title' => 'Search Results',
        'slug' => 'search',
      ]);
      $all_policies = Page::firstOrCreate([
        'title' => 'All Policies',
        'slug' => 'all-policies',
      ]);
      $terms = Page::firstOrCreate([
        'title' => 'Terms And Conditions',
        'slug' => 'terms-and-conditions',
      ]);
      $seller_agreenent = Page::firstOrCreate([
        'title' => 'Seller Agreement',
        'slug' => 'seller-agreement',
      ]);
      $privacy = Page::firstOrCreate([
        'title' => 'Privacy Policy',
        'slug' => 'privacy-policy',
      ]);
      $cookie = Page::firstOrCreate([
        'title' => 'Cookie Policy',
        'slug' => 'cookie-policy',
      ]);
      $dpa = Page::firstOrCreate([
        'title' => 'Data Processing Agreement',
        'slug' => 'data-processing-agreement',
      ]);
      $payment_policy = Page::firstOrCreate([
        'title' => 'Payment Policy',
        'slug' => 'payment-policy',
      ]);
      $copyright_policy = Page::firstOrCreate([
        'title' => 'Copyright Policy',
        'slug' => 'copyright-policy',
      ]);
      $drp = Page::firstOrCreate([
        'title' => 'Dispute Resolution Policy',
        'slug' => 'dispute-resolution-policy',
      ]);
      $favorites = Page::firstOrCreate([
        'title' => 'Favorites',
        'slug' => 'favorites',
      ]);
      $advantures = Page::firstOrCreate([
        'title' => 'All Products',
        'slug' => 'products',
      ]);
      $product = Page::firstOrCreate([
        'title' => 'Product Page',
        'slug' => 'product',
      ]);
      $cart = Page::firstOrCreate([
        'title' => 'Checkout',
        'slug' => 'checkout',
      ]);
      $payment_success = Page::firstOrCreate([
        'title' => 'Payment Success',
        'slug' => 'payment-success',
      ]);
      $payment_error = Page::firstOrCreate([
        'title' => 'Payment Error',
        'slug' => 'payment-error',
      ]);
      $profile_verify = Page::firstOrCreate([
        'title' => 'profile',
        'slug' => 'profile',
      ]);
      $profile_verify = Page::firstOrCreate([
        'title' => 'Verify Profile',
        'slug' => 'profile-verify',
      ]);
      $invite = Page::firstOrCreate([
        'title' => 'Invite',
        'slug' => 'invite',
      ]);
      $invite_referal = Page::firstOrCreate([
        'title' => 'Referal invite',
        'slug' => 'refereal',
      ]);
      $profile_purchases = Page::firstOrCreate([
        'title' => 'My Purchases',
        'slug' => 'profile-purchases',
      ]);
      $profile_purchases = Page::firstOrCreate([
        'title' => 'Referal Programm',
        'slug' => 'profile-referal',
      ]);
      $profile_settigns = Page::firstOrCreate([
        'title' => 'Profile settings',
        'slug' => 'profile-settings',
      ]);
      $gift = Page::firstOrCreate([
        'title' => 'Gift',
        'slug' => 'gift',
      ]);
    }
}
