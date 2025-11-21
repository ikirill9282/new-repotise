<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Payout;
use App\Models\User;
use Carbon\Carbon;

class PayoutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find seller user
        $seller = User::where('email', 'seller@gmail.com')->first();
        
        if (!$seller) {
            $this->command->warn('Seller user not found. Please run UserSeeder first.');
            return;
        }

        $this->command->info('Creating test payouts for seller: ' . $seller->email);

        // Create payouts with different statuses and dates
        $payouts = [
            // Paid (successful) payouts - for lifetime stats
            [
                'user_id' => $seller->id,
                'amount' => 150.00,
                'fees' => 2.75,
                'currency' => 'usd',
                'status' => Payout::STATUS_PAID,
                'processed_at' => Carbon::now()->subDays(5),
                'created_at' => Carbon::now()->subDays(6),
                'payout_method' => 'pm_test_card_1234',
                'stripe_payout_id' => 'po_test_abc123',
            ],
            [
                'user_id' => $seller->id,
                'amount' => 250.50,
                'fees' => 3.76,
                'currency' => 'usd',
                'status' => Payout::STATUS_PAID,
                'processed_at' => Carbon::now()->subDays(10),
                'created_at' => Carbon::now()->subDays(11),
                'payout_method' => 'pm_test_card_1234',
                'stripe_payout_id' => 'po_test_def456',
            ],
            [
                'user_id' => $seller->id,
                'amount' => 99.99,
                'fees' => 2.40,
                'currency' => 'usd',
                'status' => Payout::STATUS_PAID,
                'processed_at' => Carbon::now()->subDays(15),
                'created_at' => Carbon::now()->subDays(16),
                'payout_method' => 'pm_test_card_5678',
                'stripe_payout_id' => 'po_test_ghi789',
            ],
            [
                'user_id' => $seller->id,
                'amount' => 500.00,
                'fees' => 7.50,
                'currency' => 'usd',
                'status' => Payout::STATUS_PAID,
                'processed_at' => Carbon::now()->subDays(20),
                'created_at' => Carbon::now()->subDays(21),
                'payout_method' => 'pm_test_card_1234',
                'stripe_payout_id' => 'po_test_jkl012',
            ],
            
            // Processing payout
            [
                'user_id' => $seller->id,
                'amount' => 175.25,
                'fees' => 3.38,
                'currency' => 'usd',
                'status' => Payout::STATUS_PROCESSING,
                'created_at' => Carbon::now()->subDays(1),
                'payout_method' => 'pm_test_card_1234',
            ],
            
            // In Transit payout
            [
                'user_id' => $seller->id,
                'amount' => 200.00,
                'fees' => 3.80,
                'currency' => 'usd',
                'status' => Payout::STATUS_IN_TRANSIT,
                'created_at' => Carbon::now()->subDays(2),
                'payout_method' => 'pm_test_card_5678',
                'stripe_payout_id' => 'po_test_mno345',
            ],
            
            // Pending payout
            [
                'user_id' => $seller->id,
                'amount' => 125.75,
                'fees' => 2.95,
                'currency' => 'usd',
                'status' => Payout::STATUS_PENDING,
                'created_at' => Carbon::now()->subHours(6),
                'payout_method' => 'pm_test_card_1234',
            ],
            
            // Failed payout
            [
                'user_id' => $seller->id,
                'amount' => 300.00,
                'fees' => 5.70,
                'currency' => 'usd',
                'status' => Payout::STATUS_FAILED,
                'created_at' => Carbon::now()->subDays(8),
                'payout_method' => 'pm_test_card_5678',
                'failure_message' => 'Insufficient funds in the account. Please try again later.',
            ],
            
            // Canceled payout
            [
                'user_id' => $seller->id,
                'amount' => 80.50,
                'fees' => 2.13,
                'currency' => 'usd',
                'status' => Payout::STATUS_CANCELED,
                'created_at' => Carbon::now()->subDays(3),
                'payout_method' => 'pm_test_card_1234',
            ],
        ];

        foreach ($payouts as $payoutData) {
            // Calculate total_deducted if not set
            if (!isset($payoutData['total_deducted'])) {
                $payoutData['total_deducted'] = $payoutData['amount'] + $payoutData['fees'];
            }
            
            $payout = Payout::create($payoutData);
            $this->command->info('Created payout: ' . $payout->payout_id . ' - Status: ' . $payout->status . ' - Amount: $' . $payout->amount);
        }

        $this->command->info('Successfully created ' . count($payouts) . ' test payouts.');
    }
}
