<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Response;
use App\Enums\Order as EnumsOrder;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export_csv')
                ->label('Export CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    $query = $this->getTable()->getQuery();
                    $transactions = $query->with(['user', 'revenueShares.author', 'payments'])->get();
                    
                    $filename = 'transactions_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
                    
                    $headers = [
                        'Content-Type' => 'text/csv',
                        'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                    ];
                    
                    $callback = function() use ($transactions) {
                        $file = fopen('php://output', 'w');
                        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                        
                        fputcsv($file, [
                            'Transaction ID',
                            'User',
                            'Seller',
                            'Type',
                            'Amount',
                            'Currency',
                            'Status',
                            'Payment Method',
                            'Stripe Fee',
                            'Platform Commission',
                            'Seller Amount',
                            'Date',
                        ]);
                        
                        foreach ($transactions as $transaction) {
                            $payment = $transaction->payments->where('status', 'succeeded')->first();
                            $sellers = $transaction->revenueShares->pluck('author.name')->unique()->filter()->join(', ');
                            $hasSubscription = $transaction->order_products->contains(function ($op) {
                                return $op->product && $op->product->subscription;
                            });
                            
                            fputcsv($file, [
                                $transaction->id,
                                $transaction->user->name ?? '',
                                $sellers ?: '-',
                                $hasSubscription ? 'Subscription' : 'Purchase',
                                $transaction->cost,
                                strtoupper(config('cashier.currency', 'usd')),
                                $payment ? ucfirst($payment->status) : 'Pending',
                                $payment?->method ?? '-',
                                $transaction->stripe_fee ?? 0,
                                $transaction->platform_reward ?? 0,
                                $transaction->seller_reward ?? 0,
                                $transaction->created_at->format('Y-m-d H:i:s'),
                            ]);
                        }
                        
                        fclose($file);
                    };
                    
                    return Response::stream($callback, 200, $headers);
                }),
        ];
    }
}
