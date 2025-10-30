<?php

namespace App\Http\Controllers;

use App\Models\OrderProducts;
use App\Models\ProductFiles;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class OrderProductFileController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(string $orderProductId, string $fileId)
    {
        try {
            $orderProductId = (int) Crypt::decryptString($orderProductId);
            $fileId = (int) Crypt::decryptString($fileId);
        } catch (\Throwable $e) {
            abort(404);
        }

        $orderProduct = OrderProducts::with('order')
            ->whereKey($orderProductId)
            ->firstOrFail();

        if ($orderProduct->order->user_id !== Auth::id()) {
            abort(403);
        }

        $file = ProductFiles::query()
            ->whereNull('expires_at')
            ->where('product_id', $orderProduct->product_id)
            ->findOrFail($fileId);

        $disk = config('filesystems.default');

        if (!Storage::disk($disk)->exists($file->file)) {
            abort(404);
        }

        $downloadName = $file->name ?? basename($file->file);

        return Storage::disk($disk)->download($file->file, $downloadName);
    }
}
