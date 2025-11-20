<?php

namespace App\Services\Analytics;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Laravel\Cashier\Cashier;

class StripeAnalyticsService
{
    public function getActiveSellersCount(Carbon $startDate, Carbon $endDate): int
    {
        try {
            $payouts = Cashier::stripe()->payouts->all([
                'limit' => 100,
                'created' => [
                    'gte' => $startDate->timestamp,
                    'lte' => $endDate->timestamp,
                ],
            ]);

            $sellerIds = [];
            foreach ($payouts->data as $payout) {
                if ($payout->status === 'paid' && isset($payout->metadata['user_id'])) {
                    $sellerIds[] = $payout->metadata['user_id'];
                }
            }

            return count(array_unique($sellerIds));
        } catch (\Exception $e) {
            Log::error('Error fetching Stripe active sellers: ' . $e->getMessage());
            return 0;
        }
    }

    public function getPendingVerificationCount(): int
    {
        try {
            $accounts = Cashier::stripe()->accounts->all([
                'limit' => 100,
            ]);

            $pendingCount = 0;
            foreach ($accounts->data as $account) {
                if (in_array($account->details_submitted, [false, null]) ||
                    in_array($account->charges_enabled, [false]) ||
                    (isset($account->requirements['currently_due']) && !empty($account->requirements['currently_due']))) {
                    $pendingCount++;
                }
            }

            return $pendingCount;
        } catch (\Exception $e) {
            Log::error('Error fetching Stripe pending verification: ' . $e->getMessage());
            return 0;
        }
    }

    public function getPayoutsData(Carbon $startDate, Carbon $endDate): array
    {
        try {
            $payouts = Cashier::stripe()->payouts->all([
                'limit' => 100,
                'created' => [
                    'gte' => $startDate->timestamp,
                    'lte' => $endDate->timestamp,
                ],
            ]);

            $data = [];
            foreach ($payouts->data as $payout) {
                $data[] = [
                    'id' => $payout->id,
                    'amount' => $payout->amount / 100, // Convert from cents
                    'currency' => $payout->currency,
                    'status' => $payout->status,
                    'created' => Carbon::createFromTimestamp($payout->created),
                    'arrival_date' => $payout->arrival_date ? Carbon::createFromTimestamp($payout->arrival_date) : null,
                    'user_id' => $payout->metadata['user_id'] ?? null,
                ];
            }

            return $data;
        } catch (\Exception $e) {
            Log::error('Error fetching Stripe payouts: ' . $e->getMessage());
            return [];
        }
    }

    public function getConnectedAccountsCount(): int
    {
        try {
            $accounts = Cashier::stripe()->accounts->all([
                'limit' => 100,
            ]);

            return count($accounts->data);
        } catch (\Exception $e) {
            Log::error('Error fetching Stripe connected accounts: ' . $e->getMessage());
            return 0;
        }
    }

    public function getAccountStatus(string $accountId): array
    {
        try {
            $account = Cashier::stripe()->accounts->retrieve($accountId);

            return [
                'id' => $account->id,
                'charges_enabled' => $account->charges_enabled ?? false,
                'payouts_enabled' => $account->payouts_enabled ?? false,
                'details_submitted' => $account->details_submitted ?? false,
                'currently_due' => $account->requirements['currently_due'] ?? [],
                'eventually_due' => $account->requirements['eventually_due'] ?? [],
                'past_due' => $account->requirements['past_due'] ?? [],
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching Stripe account status: ' . $e->getMessage());
            return [];
        }
    }
}

