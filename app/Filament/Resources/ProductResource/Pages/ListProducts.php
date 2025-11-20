<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Models\Product;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export')
                ->label('Export CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    // Get filtered query from table
                    $query = $this->getTable()->getQuery();
                    $products = $query->with(['author', 'categories', 'status'])->get();
                    
                    $filename = 'products_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
                    
                    $headers = [
                        'Content-Type' => 'text/csv',
                        'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                    ];
                    
                    $callback = function() use ($products) {
                        $file = fopen('php://output', 'w');
                        
                        // BOM for UTF-8
                        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                        
                        // Headers
                        fputcsv($file, [
                            'ID',
                            'Title',
                            'Seller (Email)',
                            'Category',
                            'Price',
                            'Status',
                            'Created At'
                        ]);
                        
                        // Data
                        foreach ($products as $product) {
                            $categories = $product->categories->pluck('title')->join(', ');
                            fputcsv($file, [
                                $product->id,
                                $product->title,
                                $product->author->email ?? '',
                                $categories,
                                $product->price,
                                $product->status->title ?? '',
                                $product->created_at->format('Y-m-d H:i:s'),
                            ]);
                        }
                        
                        fclose($file);
                    };
                    
                    return Response::stream($callback, 200, $headers);
                }),
            
            Actions\Action::make('import')
                ->label('Import CSV')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('info')
                ->form([
                    \Filament\Forms\Components\FileUpload::make('file')
                        ->label('CSV File')
                        ->acceptedFileTypes(['text/csv', 'text/plain', 'application/vnd.ms-excel'])
                        ->required()
                        ->disk('local')
                        ->directory('imports')
                        ->visibility('private'),
                ])
                ->action(function (array $data) {
                    $this->handleImport($data['file']);
                }),
            
            Actions\CreateAction::make(),
        ];
    }
    
    protected function handleImport(string $filePath): void
    {
        $file = storage_path('app/' . $filePath);
        
        if (!file_exists($file)) {
            \Filament\Notifications\Notification::make()
                ->danger()
                ->title('File not found')
                ->body('The uploaded file could not be found.')
                ->send();
            return;
        }
        
        $handle = fopen($file, 'r');
        if (!$handle) {
            \Filament\Notifications\Notification::make()
                ->danger()
                ->title('Cannot read file')
                ->body('Unable to read the uploaded file.')
                ->send();
            return;
        }
        
        // Skip BOM if present
        $firstLine = fgets($handle);
        if (substr($firstLine, 0, 3) === chr(0xEF).chr(0xBB).chr(0xBF)) {
            $firstLine = substr($firstLine, 3);
        }
        rewind($handle);
        fseek($handle, strlen($firstLine) === strlen(fgets($handle)) ? 0 : 3);
        
        // Skip header
        fgetcsv($handle);
        
        $successCount = 0;
        $errorCount = 0;
        $errors = [];
        $lineNumber = 1;
        
        while (($row = fgetcsv($handle)) !== false) {
            $lineNumber++;
            
            if (count($row) < 4) {
                $errors[] = "Line {$lineNumber}: Insufficient columns";
                $errorCount++;
                continue;
            }
            
            [$title, $price, $categorySlug, $sellerEmail] = array_map('trim', $row);
            
            // Validate required fields
            if (empty($title) || empty($price) || empty($categorySlug) || empty($sellerEmail)) {
                $errors[] = "Line {$lineNumber}: Missing required fields (title, price, category, or seller)";
                $errorCount++;
                continue;
            }
            
            // Validate price
            if (!is_numeric($price) || $price <= 0) {
                $errors[] = "Line {$lineNumber}: Invalid price '{$price}'";
                $errorCount++;
                continue;
            }
            
            // Find category
            $category = \App\Models\Category::where('slug', $categorySlug)->first();
            if (!$category) {
                $errors[] = "Line {$lineNumber}: Category '{$categorySlug}' not found";
                $errorCount++;
                continue;
            }
            
            // Find seller
            $seller = \App\Models\User::where('email', $sellerEmail)->first();
            if (!$seller) {
                $errors[] = "Line {$lineNumber}: Seller with email '{$sellerEmail}' not found";
                $errorCount++;
                continue;
            }
            
            // Check for duplicate by slug
            $slug = \App\Helpers\Slug::makeEn($title);
            if (Product::where('slug', $slug)->exists()) {
                $errors[] = "Line {$lineNumber}: Product with slug '{$slug}' already exists (duplicate)";
                $errorCount++;
                continue;
            }
            
            // Create product
            try {
                $product = Product::create([
                    'title' => $title,
                    'slug' => $slug,
                    'price' => $price,
                    'user_id' => $seller->id,
                    'status_id' => 3, // Pending Review
                    'text' => '', // Empty description
                ]);
                
                $product->categories()->attach($category->id);
                
                $successCount++;
            } catch (\Exception $e) {
                $errors[] = "Line {$lineNumber}: Error creating product - " . $e->getMessage();
                $errorCount++;
            }
        }
        
        fclose($handle);
        
        // Clean up file
        @unlink($file);
        
        // Show results
        $message = "Import completed: {$successCount} successful, {$errorCount} errors.";
        if (!empty($errors)) {
            $message .= "\n\nErrors:\n" . implode("\n", array_slice($errors, 0, 10));
            if (count($errors) > 10) {
                $message .= "\n... and " . (count($errors) - 10) . " more errors.";
            }
        }
        
        \Filament\Notifications\Notification::make()
            ->title('Import Results')
            ->body($message)
            ->success($errorCount === 0)
            ->warning($errorCount > 0)
            ->persistent()
            ->send();
    }
}
