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
              'text' => 'TrekGuider is for travelers seeking expert-created digital guides and travel creators looking to sell their content and build their business online.'
            ]
          ],
          [
            'parent_id' => null,
            'type' => 'question',
            'text' => 'How do I get started on TrekGuider?',
            'answer' => [
              'type' => 'answer',
              'text' => 'Getting started is easy! Travelers: Explore our catalog and create a free account to save favorites and manage purchases. Creators: Visit the "For Creators" page to learn about registration and selling.'
            ]
          ],
          [
            'parent_id' => null,
            'type' => 'question',
            'text' => 'Can I share a product as a gift for someone?',
            'answer' => [
              'type' => 'answer',
              'text' => 'Yes, gifting is available! During checkout, select "Purchase as a Gift" and enter the recipient"s email. They"ll receive a special gift notification and access to download their travel product.'
            ]
          ],
          [
            'parent_id' => null,
            'type' => 'question',
            'text' => 'How does the Referral Program work: earnings, participation, and tracking?',
            'answer' => [
              'type' => 'answer',
              'text' => "The TrekGuider Referral Program offers unlimited earning potential by rewarding you for referring new users! Get 25% of the platform commission from referred creator sales for their first 30 days, and 12.5% for the next 11 months. Withdraw earnings anytime (min. $40, small Stripe fee). Anyone can join (some sanctioned regions excluded) – refer buyers or creators. Monitor your earnings and progress in the 'Referral Program' section of your account dashboard. See the dedicated 'Referral Program' page, 'Terms & Conditions' and Referral Program Policy for complete details."
            ]
          ],
          [
            'parent_id' => null,
            'type' => 'question',
            'text' => 'How do I get help and support from TrekGuider?',
            'answer' => [
              'type' => 'answer',
              'text' => "For help and support, please use the contact form on our 'Help Center' page. Provide detailed information about your inquiry for prompt assistance."
            ]
          ],
          [
            'parent_id' => null,
            'type' => 'question',
            'text' => 'I am experiencing a technical issue with the platform. What should I do?',
            'answer' => [
              'type' => 'answer',
              'text' => "Report technical issues via the 'Help Center' contact form. Include a detailed description, steps to reproduce, error messages, and screenshots if possible."
            ]
          ],
          [
            'parent_id' => null,
            'type' => 'question',
            'text' => 'Interested in Partnerships or Investments with TrekGuider?',
            'answer' => [
              'type' => 'answer',
              'text' => "For all partnership and investment inquiries, please visit our dedicated 'For Investors & Partners' page and complete the relevant inquiry form. Our team will review your submission and be in touch to discuss opportunities."
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
              'text' => "After purchase, you'll receive a confirmation email with a download link. You can also download anytime from 'My Purchases' in your account dashboard."
            ]
          ],
          [
            'parent_id' => null,
            'type' => 'question',
            'text' => 'I am having trouble with payment. What should I do?',
            'answer' => [
              'type' => 'answer',
              'text' => 'If you encounter payment issues, we recommend the following troubleshooting steps:
								Verify Payment Details: Double-check that your payment information (card number, expiration date, CVV, billing address) is accurate.
								Check Funds: Ensure sufficient funds are available in your account.
								Alternative Payment Method: Try using a different payment card or method if possible.
								Contact Payment Provider: Contact your bank or card issuer to rule out any potential issues on their end.
								If these steps do not resolve the issue, please contact our support team via the "Help Center" page for further assistance.'
            ]
          ],
          [
            'parent_id' => null,
            'type' => 'question',
            'text' => 'How do I delete my TrekGuider account?',
            'answer' => [
              'type' => 'answer',
              'text' => "Delete your account in 'Account Settings' > 'Delete Account'. Please note that account deletion is permanent and will result in the loss of purchase history and account data. Ensure you are certain before proceeding with account deletion."
            ]
          ],
          [
            'parent_id' => null,
            'type' => 'question',
            'text' => 'How do I cancel a subscription?',
            'answer' => [
              'type' => 'answer',
              'text' => "Cancel subscriptions in 'My Purchases & Subscriptions'. Select the subscription and click 'Cancel'. You won't be charged further and will retain access until the end of the current billing cycle."
            ]
          ],
          [
            'parent_id' => null,
            'type' => 'question',
            'text' => 'The product I purchased is not what I expected. How do I get a refund?',
            'answer' => [
              'type' => 'answer',
              'text' => "Refunds are based on seller policies, found on each product page. If eligible, request a refund in 'My Purchases'. For disputes, contact Support via the 'Help Center."
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
              'text' => "TrekGuider welcomes travel content creators aged 18+ from most countries worldwide (some sanctioned regions excluded). Sell any digital travel content format valuable to travelers – guides, itineraries, maps, and more – offering one-time purchases or subscriptions. Verification is straightforward: basic verification (personal & tax info) is needed for payouts over $40. See the 'For Creators' page for details and requirements."
            ]
          ],
          [
            'parent_id' => null,
            'type' => 'question',
            'text' => 'What is my Creator Page and how do donations work?',
            'answer' => [
              'type' => 'answer',
              'text' => 'Your Creator Page is your professional landing page on TrekGuider – showcase your brand, expertise, and products in one place! Enable the tips feature on your Creator Page to allow fans to support you directly with one-time or recurring donations.'
            ]
          ],
          [
            'parent_id' => null,
            'type' => 'question',
            'text' => 'What fees and commissions does TrekGuider charge, and how can I reduce my commission?',
            'answer' => [
              'type' => 'answer',
              'text' => "TrekGuider is committed to a transparent and competitive fee structure designed to maximize creator earnings. Our fees are as follows:
							Platform Commission: This is a tiered commission, ranging from 4% to 10% of the product sale price, and is determined by your seller level. Reduce Your Commission: Easily lower your platform commission by increasing your sales volume! Our tiered system automatically reduces your commission rate as your sales grow – progress from Level 1 (10% commission) to Level 3 (5% commission) by reaching set sales thresholds. Once you reach a level, that lower rate is locked in permanently. New sellers enjoy a reduced 5% commission for the first 30 days!
							Stripe Payment Processing Fee (Transaction Fee): Applied by Stripe for handling transactions, this fee varies based on the buyer's payment method and region. For U.S. cards, it's typically 2.9% + $0.30 per transaction. International cards incur an additional 1.5%. See Stripe's Fee Schedule for a detailed breakdown.
							Payout Fee (Stripe Payout Processing Fee): A small Stripe fee for processing payouts: 0.25% + $0.25 for standard payouts, 1% for instant payouts.
							Currency Conversion Fee (if applicable): A 1% Stripe fee applies if currency conversion is needed for payout (e.g., payout currency differs from USD).
							Form 1099-K Issuance Fee (U.S. Sellers): U.S. sellers earning over $600 annually incur a $2.99 annual fee for automated Form 1099-K generation.
							Stripe payment processing fees are industry-standard and are not controlled by TrekGuider."
            ]
          ],
          [
            'parent_id' => null,
            'type' => 'question',
            'text' => 'How does TrekGuider protect my content and copyrights?',
            'answer' => [
              'type' => 'answer',
              'text' => 'TrekGuider protects your content with U.S. copyright law and advanced technical security measures. Your copyrights are rigorously protected on our platform.'
            ]
          ],
          [
            'parent_id' => null,
            'type' => 'question',
            'text' => 'Who is responsible for licenses, taxes, and legal permits?',
            'answer' => [
              'type' => 'answer',
              'text' => 'Sellers are responsible for all licenses, permits, and taxes related to their business operations. Ensure you comply with all applicable laws and regulations.'
            ]
          ],
          [
            'parent_id' => null,
            'type' => 'question',
            'text' => 'Does TrekGuider provide tax forms and reporting for sellers?',
            'answer' => [
              'type' => 'answer',
              'text' => 'TrekGuider provides automated Form 1099-K for U.S. sellers earning over $600 annually. Non-U.S. sellers are responsible for their own tax reporting. More information is available on the "For Creators" page.'
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
