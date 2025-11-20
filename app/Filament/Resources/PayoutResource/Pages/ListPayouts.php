<?php

namespace App\Filament\Resources\PayoutResource\Pages;

use App\Filament\Resources\PayoutResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Response;
use App\Models\Payout;

class ListPayouts extends ListRecords
{
    protected static string $resource = PayoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export_csv')
                ->label('Export CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    $query = $this->getTable()->getQuery();
                    $payouts = $query->with('user')->get();
                    
                    $filename = 'payouts_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
                    
                    $headers = [
                        'Content-Type' => 'text/csv',
                        'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                    ];
                    
                    $callback = function() use ($payouts) {
                        $file = fopen('php://output', 'w');
                        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM for UTF-8
                        
                        fputcsv($file, [
                            'Payout ID',
                            'Seller',
                            'Amount',
                            'Currency',
                            'Status',
                            'Stripe Payout ID',
                            'Date Created',
                            'Date Processed',
                            'Failure Message',
                        ]);
                        
                        foreach ($payouts as $payout) {
                            fputcsv($file, [
                                $payout->id,
                                $payout->user->name ?? '',
                                $payout->amount,
                                $payout->currency,
                                ucfirst($payout->status),
                                $payout->stripe_payout_id ?? '',
                                $payout->created_at->format('Y-m-d H:i:s'),
                                $payout->processed_at?->format('Y-m-d H:i:s') ?? '',
                                $payout->failure_message ?? '',
                            ]);
                        }
                        
                        fclose($file);
                    };
                    
                    return Response::stream($callback, 200, $headers);
                }),
        ];
    }
}
