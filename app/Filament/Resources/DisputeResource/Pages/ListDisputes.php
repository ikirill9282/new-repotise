<?php

namespace App\Filament\Resources\DisputeResource\Pages;

use App\Filament\Resources\DisputeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Response;

class ListDisputes extends ListRecords
{
    protected static string $resource = DisputeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export_csv')
                ->label('Export CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    $query = $this->getTable()->getQuery();
                    $disputes = $query->with(['buyer', 'seller', 'order'])->get();
                    
                    $filename = 'disputes_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
                    
                    $headers = [
                        'Content-Type' => 'text/csv',
                        'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                    ];
                    
                    $callback = function() use ($disputes) {
                        $file = fopen('php://output', 'w');
                        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                        
                        fputcsv($file, [
                            'Dispute ID',
                            'Order ID',
                            'Buyer',
                            'Seller',
                            'Subject',
                            'Status',
                            'Date Created',
                            'Date Resolved',
                        ]);
                        
                        foreach ($disputes as $dispute) {
                            fputcsv($file, [
                                $dispute->id,
                                $dispute->order_id,
                                $dispute->buyer->name ?? '',
                                $dispute->seller->name ?? '',
                                $dispute->subject,
                                match($dispute->status) {
                                    \App\Models\Dispute::STATUS_OPEN => 'Open',
                                    \App\Models\Dispute::STATUS_IN_REVIEW => 'In Review',
                                    \App\Models\Dispute::STATUS_RESOLVED_BUYER => 'Resolved (Buyer)',
                                    \App\Models\Dispute::STATUS_RESOLVED_SELLER => 'Resolved (Seller)',
                                    default => ucfirst($dispute->status),
                                },
                                $dispute->created_at->format('Y-m-d H:i:s'),
                                $dispute->resolved_at?->format('Y-m-d H:i:s') ?? '',
                            ]);
                        }
                        
                        fclose($file);
                    };
                    
                    return Response::stream($callback, 200, $headers);
                }),
        ];
    }
}
