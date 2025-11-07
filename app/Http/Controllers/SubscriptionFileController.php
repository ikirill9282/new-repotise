<?php

namespace App\Http\Controllers;

use App\Models\ProductFiles;
use App\Models\Subscriptions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class SubscriptionFileController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(string $subscriptionId, string $fileId)
    {
        try {
            $subscriptionId = (int) Crypt::decryptString($subscriptionId);
            $fileId = (int) Crypt::decryptString($fileId);
        } catch (\Throwable $e) {
            abort(404);
        }

        $subscription = Subscriptions::query()
            ->whereKey($subscriptionId)
            ->firstOrFail();

        if ($subscription->user_id !== Auth::id()) {
            abort(403);
        }

        $productId = $this->resolveProductId($subscription);
        if (!$productId) {
            abort(404);
        }

        $file = ProductFiles::query()
            ->whereNull('expires_at')
            ->where('product_id', $productId)
            ->findOrFail($fileId);

        $disk = config('filesystems.default');

        if (!Storage::disk($disk)->exists($file->file)) {
            abort(404);
        }

        $downloadName = $file->name ?? basename($file->file);

        return Storage::disk($disk)->download($file->file, $downloadName);
    }

    protected function resolveProductId(Subscriptions $subscription): ?int
    {
        $type = $subscription->type ?? '';
        if (!str_starts_with($type, 'plan_')) {
            return null;
        }

        $parts = explode('_', $type);

        return isset($parts[2]) ? (int) $parts[2] : null;
    }
}
