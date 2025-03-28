<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FAQ;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      $data = [
        'general' => [
          [
            'parent_id' => null,
            'type' => 'question',
            'text' => 'What is TrekGuider and what does it offer?',
            'answer' => [
              'type' => 'answer',
              'text' => 'TrekGuider is the online marketplace connecting travelers with expert creators of digital travel guides, itineraries, maps, and more. We empower creators to monetize their expertise and provide travelers with premium resources for unforgettable journeys.',
            ]
          ],
          [
            'parent_id' => null,
            'type' => 'question',
            'text' => 'Who is TrekGuider for?',
            'answer' => [
              'type' => 'answer',
              'text' => 'TrekGuider is the online marketplace connecting travelers with expert creators of digital travel guides, itineraries, maps, and more. We empower creators to monetize their expertise and provide travelers with premium resources for unforgettable journeys.'
            ]
          ],
          [
            'parent_id' => null,
            'type' => 'question',
            'text' => 'How do I get started on TrekGuider?',
            'answer' => [
              'type' => 'answer',
              'text' => 'TrekGuider is the online marketplace connecting travelers with expert creators of digital travel guides, itineraries, maps, and more. We empower creators to monetize their expertise and provide travelers with premium resources for unforgettable journeys.'
            ]
          ],
          [
            'parent_id' => null,
            'type' => 'question',
            'text' => 'Can I share a product as a gift for someone?',
            'answer' => [
              'type' => 'answer',
              'text' => 'TrekGuider is the online marketplace connecting travelers with expert creators of digital travel guides, itineraries, maps, and more. We empower creators to monetize their expertise and provide travelers with premium resources for unforgettable journeys.'
            ]
          ],
          [
            'parent_id' => null,
            'type' => 'question',
            'text' => 'How does the Referral Program work: earnings, participation, and tracking?',
            'answer' => [
              'type' => 'answer',
              'text' => 'TrekGuider is the online marketplace connecting travelers with expert creators of digital travel guides, itineraries, maps, and more. We empower creators to monetize their expertise and provide travelers with premium resources for unforgettable journeys.'
            ]
          ],
          [
            'parent_id' => null,
            'type' => 'question',
            'text' => 'How do I get help and support from TrekGuider?',
            'answer' => [
              'type' => 'answer',
              'text' => 'TrekGuider is the online marketplace connecting travelers with expert creators of digital travel guides, itineraries, maps, and more. We empower creators to monetize their expertise and provide travelers with premium resources for unforgettable journeys.'
            ]
          ],
          [
            'parent_id' => null,
            'type' => 'question',
            'text' => 'I am experiencing a technical issue with the platform. What should I do?',
            'answer' => [
              'type' => 'answer',
              'text' => 'TrekGuider is the online marketplace connecting travelers with expert creators of digital travel guides, itineraries, maps, and more. We empower creators to monetize their expertise and provide travelers with premium resources for unforgettable journeys.'
            ]
          ],
        ],
        'customer' => [
          [
            'parent_id' => null,
            'type' => 'question',
            'text' => 'How do I download a purchased product?',
            'answer' => [
              'type' => 'answer',
              'text' => 'TrekGuider is the online marketplace connecting travelers with expert creators of digital travel guides, itineraries, maps, and more. We empower creators to monetize their expertise and provide travelers with premium resources for unforgettable journeys.'
            ]
          ],
          [
            'parent_id' => null,
            'type' => 'question',
            'text' => 'I am having trouble with payment. What should I do?',
            'answer' => [
              'type' => 'answer',
              'text' => 'TrekGuider is the online marketplace connecting travelers with expert creators of digital travel guides, itineraries, maps, and more. We empower creators to monetize their expertise and provide travelers with premium resources for unforgettable journeys.'
            ]
          ],
          [
            'parent_id' => null,
            'type' => 'question',
            'text' => 'How do I delete my TrekGuider account?',
            'answer' => [
              'type' => 'answer',
              'text' => 'TrekGuider is the online marketplace connecting travelers with expert creators of digital travel guides, itineraries, maps, and more. We empower creators to monetize their expertise and provide travelers with premium resources for unforgettable journeys.'
            ]
          ],
          [
            'parent_id' => null,
            'type' => 'question',
            'text' => 'How do I cancel a subscription?',
            'answer' => [
              'type' => 'answer',
              'text' => 'TrekGuider is the online marketplace connecting travelers with expert creators of digital travel guides, itineraries, maps, and more. We empower creators to monetize their expertise and provide travelers with premium resources for unforgettable journeys.'
            ]
          ],
          [
            'parent_id' => null,
            'type' => 'question',
            'text' => 'The product I purchased is not what I expected. How do I get a refund?',
            'answer' => [
              'type' => 'answer',
              'text' => 'TrekGuider is the online marketplace connecting travelers with expert creators of digital travel guides, itineraries, maps, and more. We empower creators to monetize their expertise and provide travelers with premium resources for unforgettable journeys.'
            ]
          ],
        ],
        'creator' => [
          [
            'parent_id' => null,
            'type' => 'question',
            'text' => 'Who can become a seller, what can I sell, and how does verification work?',
            'answer' => [
              'type' => 'answer',
              'text' => 'TrekGuider is the online marketplace connecting travelers with expert creators of digital travel guides, itineraries, maps, and more. We empower creators to monetize their expertise and provide travelers with premium resources for unforgettable journeys.'
            ]
          ],
          [
            'parent_id' => null,
            'type' => 'question',
            'text' => 'What is my Creator Page and how do donations work?',
            'answer' => [
              'type' => 'answer',
              'text' => 'TrekGuider is the online marketplace connecting travelers with expert creators of digital travel guides, itineraries, maps, and more. We empower creators to monetize their expertise and provide travelers with premium resources for unforgettable journeys.'
            ]
          ],
          [
            'parent_id' => null,
            'type' => 'question',
            'text' => 'What fees and commissions does TrekGuider charge, and how can I reduce my commission?',
            'answer' => [
              'type' => 'answer',
              'text' => 'TrekGuider is the online marketplace connecting travelers with expert creators of digital travel guides, itineraries, maps, and more. We empower creators to monetize their expertise and provide travelers with premium resources for unforgettable journeys.'
            ]
          ],
          [
            'parent_id' => null,
            'type' => 'question',
            'text' => 'How does TrekGuider protect my content and copyrights?',
            'answer' => [
              'type' => 'answer',
              'text' => 'TrekGuider is the online marketplace connecting travelers with expert creators of digital travel guides, itineraries, maps, and more. We empower creators to monetize their expertise and provide travelers with premium resources for unforgettable journeys.'
            ]
          ],
          [
            'parent_id' => null,
            'type' => 'question',
            'text' => 'Who is responsible for licenses, taxes, and legal permits?',
            'answer' => [
              'type' => 'answer',
              'text' => 'TrekGuider is the online marketplace connecting travelers with expert creators of digital travel guides, itineraries, maps, and more. We empower creators to monetize their expertise and provide travelers with premium resources for unforgettable journeys.'
            ]
          ],
          [
            'parent_id' => null,
            'type' => 'question',
            'text' => 'Does TrekGuider provide tax forms and reporting for sellers?',
            'answer' => [
              'type' => 'answer',
              'text' => 'TrekGuider is the online marketplace connecting travelers with expert creators of digital travel guides, itineraries, maps, and more. We empower creators to monetize their expertise and provide travelers with premium resources for unforgettable journeys.'
            ]
          ],
        ],
      ];

      foreach ($data as $group => $items) {
        foreach ($items as &$item) {
          $item['group'] = $group;
          $question = FAQ::firstOrCreate(
            ['text' => $item['text'], 'group' => $item['group']],
            [
              'parent_id' => $item['parent_id'],
              'type' => $item['type'],
              'text' => $item['text'],
              'group' => $item['group'],
            ]
          );

          if (!empty($item['answer'])) {
            $answer_item = $item['answer'];
            $answer_item['parent_id'] = $question->id;
            $answer = FAQ::firstOrCreate(
              ['parent_id' => $answer_item['parent_id'], 'text' => $answer_item['text']],
              [
                'parent_id' => $answer_item['parent_id'],
                'type' => $answer_item['type'],
                'text' => $answer_item['text'],
                'group' => $item['group'],
              ]
            );
          }
        }
      }
    }
}
