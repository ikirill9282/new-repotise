<?php

namespace App\Filament\Resources\RefundRequestResource\Pages;

use App\Filament\Resources\RefundRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Response;

class ListRefundRequests extends ListRecords
{
    protected static string $resource = RefundRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export_csv')
                ->label('Export CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    $query = $this->getTable()->getQuery();
                    $refunds = $query->with(['buyer', 'seller', 'order'])->get();
                    
                    $filename = 'refund_requests_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
                    
                    $headers = [
                        'Content-Type' => 'text/csv',
                        'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                    ];
                    
                    $callback = function() use ($refunds) {
                        $file = fopen('php://output', 'w');
                        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                        
                        fputcsv($file, [
                            'Refund ID',
                            'Order ID',
                            'Buyer',
                            'Seller',
                            'Amount',
                            'Reason',
                            'Status',
                            'Date Requested',
                            'Date Resolved',
                            'Stripe Refund ID',
                        ]);
                        
                        foreach ($refunds as $refund) {
                            fputcsv($file, [
                                $refund->id,
                                $refund->order_id,
                                $refund->buyer->name ?? '',
                                $refund->seller->name ?? '',
                                $refund->refund_amount ?? 0,
                                $refund->reason ?? '',
                                ucfirst($refund->status),
                                $refund->created_at->format('Y-m-d H:i:s'),
                                $refund->resolved_at?->format('Y-m-d H:i:s') ?? '',
                                $refund->stripe_refund_id ?? '',
                            ]);
                        }
                        
                        fclose($file);
                    };
                    
                    return Response::stream($callback, 200, $headers);
                }),
        ];
    }
}
