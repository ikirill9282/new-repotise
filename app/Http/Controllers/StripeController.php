<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use Laravel\Cashier\Cashier;
use App\Models\Subscriptions;
use App\Models\Product;
use App\Models\User;
use App\Models\UserNotification;
use App\Mail\SubscriptionPaymentReceipt;
use App\Mail\SubscriptionPaymentIssue;
use App\Mail\SubscriptionCanceled;
use App\Mail\SubscriptionExpired;
use App\Mail\RecurringDonationPaymentReceipt;
use App\Mail\RecurringDonationPaymentIssue;
use App\Mail\RecurringDonationCanceled;
use App\Models\UserFunds;
use App\Models\Payments;
use App\Models\RevenueShare;
use Illuminate\Support\Str;
use App\Services\StripePayoutProcessor;
use App\Models\Payout;
use App\Mail\PayoutOnItsWay;
use App\Mail\PayoutIssue;
use App\Services\StripeWithdrawalProcessor;

class StripeController extends Controller
{
    public function hook(Request $request)
    {
      $event = $request->attributes->get('stripe_event');

      Log::channel('stripe_events')->debug('Stripe Event', ['type' => $event?->type, 'id' => $event?->id]);

      try {
        $type = $event?->type ?? null;
        $object = $event?->data?->object ?? null;

        if (!$type || !$object) {
          return response('ok');
        }

        // Subscriptions / Invoices
        if ($type === 'invoice.payment_succeeded') {
          $this->handleInvoicePaymentSucceeded($object);
        } elseif ($type === 'invoice.payment_failed') {
          $this->handleInvoicePaymentFailed($object);
        } elseif ($type === 'customer.subscription.updated') {
          $previous = $event?->data?->previous_attributes ?? null;
          $this->handleSubscriptionUpdated($object, $previous);
        } elseif ($type === 'customer.subscription.deleted') {
          $this->handleSubscriptionDeleted($object);
        } elseif ($type === 'customer.subscription.created') {
          $this->handleSubscriptionCreated($object);
        } elseif ($type === 'payout.created') {
          $this->handleStripePayoutEvent($object, $type);
        } elseif ($type === 'payout.updated') {
          $this->handleStripePayoutEvent($object, $type);
        } elseif ($type === 'payout.failed') {
          $this->handleStripePayoutEvent($object, $type);
        }
      } catch (\Throwable $e) {
        Log::channel('stripe_events')->warning('Stripe hook handler failed', [
          'error' => $e->getMessage(),
        ]);
      }

      return response('ok');
    }

    protected function handleInvoicePaymentSucceeded($invoice): void
    {
      // IV: recurring donations (subscription with metadata.type=donation_recurring)
      $stripeSubscriptionId = $invoice->subscription ?? null;
      if ($stripeSubscriptionId) {
        try {
          $stripeSub = Cashier::stripe()->subscriptions->retrieve($stripeSubscriptionId);
          $subMeta = $stripeSub?->metadata ?? null;
          $subMeta = $subMeta instanceof \Stripe\StripeObject ? $subMeta->toArray() : (array) $subMeta;
          if (($subMeta['type'] ?? null) === 'donation_recurring') {
            $this->handleRecurringDonationInvoiceSucceeded($invoice, $stripeSub);
            return;
          }
        } catch (\Throwable $e) {
          // ignore and continue with product subscriptions
        }
      }

      // Only send receipt for renewals (TЗ III.email.2)
      $billingReason = $invoice->billing_reason ?? null;
      if ($billingReason !== 'subscription_cycle') {
        return;
      }

      $stripeSubscriptionId = $invoice->subscription ?? null;
      if (!$stripeSubscriptionId) {
        return;
      }

      $subscription = Subscriptions::where('stripe_id', $stripeSubscriptionId)->first();
      if (!$subscription) {
        return;
      }

      $user = User::find($subscription->user_id);
      if (!$user) {
        return;
      }

      $product = $this->resolveProductFromSubscriptionType($subscription->type);
      if (!$product) {
        return;
      }

      $amountPaid = isset($invoice->amount_paid) ? ((float) $invoice->amount_paid / 100) : null;
      $amountPaidLabel = $amountPaid !== null ? ('$' . number_format($amountPaid, 2)) : '$0.00';

      $periodStart = isset($invoice->period_start) ? Carbon::createFromTimestamp((int) $invoice->period_start) : null;
      $periodEnd = isset($invoice->period_end) ? Carbon::createFromTimestamp((int) $invoice->period_end) : null;
      $billingPeriodLabel = ($periodStart && $periodEnd)
        ? ($periodStart->format('m.d.Y') . ' - ' . $periodEnd->format('m.d.Y'))
        : 'Current period';

      $nextBillingDateLabel = $periodEnd ? $periodEnd->format('m.d.Y') : null;

      try {
        Mail::to($user->email)->send(new SubscriptionPaymentReceipt(
          $user,
          $product,
          $amountPaidLabel,
          $billingPeriodLabel,
          $nextBillingDateLabel,
        ));
      } catch (\Throwable $e) {
        Log::warning('Failed to send subscription payment receipt', [
          'subscription_id' => $subscription->id,
          'stripe_subscription' => $stripeSubscriptionId,
          'error' => $e->getMessage(),
        ]);
      }
    }

    protected function handleInvoicePaymentFailed($invoice): void
    {
      // IV: recurring donations payment failed
      $stripeSubscriptionId = $invoice->subscription ?? null;
      if ($stripeSubscriptionId) {
        try {
          $stripeSub = Cashier::stripe()->subscriptions->retrieve($stripeSubscriptionId);
          $subMeta = $stripeSub?->metadata ?? null;
          $subMeta = $subMeta instanceof \Stripe\StripeObject ? $subMeta->toArray() : (array) $subMeta;
          if (($subMeta['type'] ?? null) === 'donation_recurring') {
            $this->handleRecurringDonationInvoiceFailed($invoice, $stripeSub);
            return;
          }
        } catch (\Throwable $e) {
          // ignore and continue with product subscriptions
        }
      }

      $stripeSubscriptionId = $invoice->subscription ?? null;
      if (!$stripeSubscriptionId) {
        return;
      }

      $subscription = Subscriptions::where('stripe_id', $stripeSubscriptionId)->first();
      if (!$subscription) {
        return;
      }

      $user = User::find($subscription->user_id);
      if (!$user) {
        return;
      }

      $product = $this->resolveProductFromSubscriptionType($subscription->type);
      if (!$product) {
        return;
      }

      // III.email.3 (TЗ): Payment issue for subscription
      try {
        Mail::to($user->email)->send(new SubscriptionPaymentIssue($user, $product));
      } catch (\Throwable $e) {
        Log::warning('Failed to send subscription payment issue email', [
          'subscription_id' => $subscription->id,
          'stripe_subscription' => $stripeSubscriptionId,
          'error' => $e->getMessage(),
        ]);
      }
    }

    protected function handleSubscriptionCreated($stripeSubscription): void
    {
      $stripeSubscriptionId = $stripeSubscription->id ?? null;
      if (!$stripeSubscriptionId) {
        return;
      }

      // IV.toast.7 (fallback): recurring donation created
      $subMeta = $stripeSubscription->metadata ?? null;
      $subMeta = $subMeta instanceof \Stripe\StripeObject ? $subMeta->toArray() : (array) $subMeta;
      if (($subMeta['type'] ?? null) === 'donation_recurring') {
        $sellerId = isset($subMeta['seller_id']) ? (int) $subMeta['seller_id'] : null;
        if ($sellerId) {
          $this->createToastNotification(
            $sellerId,
            'New Recurring Supporter! 🌟',
            'Awesome! Someone has started a recurring donation to support you.',
            'success',
          );
        }
        return;
      }

      $subscription = Subscriptions::where('stripe_id', $stripeSubscriptionId)->first();
      if (!$subscription) {
        return;
      }

      $product = $this->resolveProductFromSubscriptionType($subscription->type);
      if (!$product) {
        return;
      }

      // III.toast.7 (TЗ): New Subscriber! 🥳 (seller)
      $sellerId = $product->user_id ?? null;
      if ($sellerId) {
        $this->createToastNotification(
          $sellerId,
          'New Subscriber! 🥳',
          'Awesome! Someone just subscribed to your product: "' . e($product->title) . '"!',
          'success',
        );
      }
    }

    protected function handleSubscriptionUpdated($stripeSubscription, $previousAttributes = null): void
    {
      $stripeSubscriptionId = $stripeSubscription->id ?? null;
      if (!$stripeSubscriptionId) {
        return;
      }

      // IV.email.5 + IV.toast.8: recurring donation canceled
      $subMeta = $stripeSubscription->metadata ?? null;
      $subMeta = $subMeta instanceof \Stripe\StripeObject ? $subMeta->toArray() : (array) $subMeta;
      if (($subMeta['type'] ?? null) === 'donation_recurring') {
        $this->handleRecurringDonationSubscriptionUpdated($stripeSubscription, $previousAttributes);
        return;
      }

      $subscription = Subscriptions::where('stripe_id', $stripeSubscriptionId)->first();
      if (!$subscription) {
        return;
      }

      $user = User::find($subscription->user_id);
      $product = $this->resolveProductFromSubscriptionType($subscription->type);
      if (!$product || !$user) {
        return;
      }

      $cancelAtPeriodEnd = (bool) ($stripeSubscription->cancel_at_period_end ?? false);
      $prevCancelAtPeriodEnd = null;
      if (is_object($previousAttributes)) {
        $prevCancelAtPeriodEnd = $previousAttributes->cancel_at_period_end ?? null;
      } elseif (is_array($previousAttributes)) {
        $prevCancelAtPeriodEnd = $previousAttributes['cancel_at_period_end'] ?? null;
      }

      if ($cancelAtPeriodEnd && $prevCancelAtPeriodEnd === false) {
        $end = isset($stripeSubscription->current_period_end) ? Carbon::createFromTimestamp((int) $stripeSubscription->current_period_end) : null;
        $endLabel = $end ? $end->format('m.d.Y') : null;

        // III.email.4 (TЗ): Subscription canceled
        try {
          Mail::to($user->email)->send(new SubscriptionCanceled($user, $product, $endLabel));
        } catch (\Throwable $e) {
          Log::warning('Failed to send subscription canceled email', [
            'subscription_id' => $subscription->id,
            'stripe_subscription' => $stripeSubscriptionId,
            'error' => $e->getMessage(),
          ]);
        }

        // III.toast.8 (TЗ): Subscription Canceled (seller)
        $sellerId = $product->user_id ?? null;
        if ($sellerId) {
          $this->createToastNotification(
            $sellerId,
            'Subscription Canceled',
            'A user has canceled their subscription to your product: "' . e($product->title) . '".',
            'info',
          );
        }
      }
    }

    protected function handleSubscriptionDeleted($stripeSubscription): void
    {
      $stripeSubscriptionId = $stripeSubscription->id ?? null;
      if (!$stripeSubscriptionId) {
        return;
      }

      // IV.email.5 + IV.toast.8: recurring donation deleted
      $subMeta = $stripeSubscription->metadata ?? null;
      $subMeta = $subMeta instanceof \Stripe\StripeObject ? $subMeta->toArray() : (array) $subMeta;
      if (($subMeta['type'] ?? null) === 'donation_recurring') {
        $this->handleRecurringDonationSubscriptionDeleted($stripeSubscription);
        return;
      }

      $subscription = Subscriptions::where('stripe_id', $stripeSubscriptionId)->first();
      if (!$subscription) {
        return;
      }

      $user = User::find($subscription->user_id);
      $product = $this->resolveProductFromSubscriptionType($subscription->type);
      if (!$product || !$user) {
        return;
      }

      $currentPeriodEnd = isset($stripeSubscription->current_period_end)
        ? Carbon::createFromTimestamp((int) $stripeSubscription->current_period_end)
        : null;

      $endedAt = isset($stripeSubscription->ended_at) && $stripeSubscription->ended_at
        ? Carbon::createFromTimestamp((int) $stripeSubscription->ended_at)
        : null;

      // Heuristic:
      // - If period already ended -> expired email (III.email.5)
      // - Otherwise treat as cancellation confirmation (III.email.4)
      $now = Carbon::now();
      $isExpired = $currentPeriodEnd ? $currentPeriodEnd->isPast() : ($endedAt ? $endedAt->isPast() : true);

      if ($isExpired) {
        $expLabel = ($currentPeriodEnd ?? $endedAt)?->format('m.d.Y');
        try {
          Mail::to($user->email)->send(new SubscriptionExpired($user, $product, $expLabel));
        } catch (\Throwable $e) {
          Log::warning('Failed to send subscription expired email', [
            'subscription_id' => $subscription->id,
            'stripe_subscription' => $stripeSubscriptionId,
            'error' => $e->getMessage(),
          ]);
        }
      } else {
        $endLabel = $currentPeriodEnd?->format('m.d.Y');
        try {
          Mail::to($user->email)->send(new SubscriptionCanceled($user, $product, $endLabel));
        } catch (\Throwable $e) {
          Log::warning('Failed to send subscription canceled email (deleted)', [
            'subscription_id' => $subscription->id,
            'stripe_subscription' => $stripeSubscriptionId,
            'error' => $e->getMessage(),
          ]);
        }
      }
    }

    protected function resolveProductFromSubscriptionType(?string $type): ?Product
    {
      if (!$type || !str_starts_with($type, 'plan_')) {
        return null;
      }

      $parts = explode('_', $type);
      $productId = isset($parts[2]) ? (int) $parts[2] : null;
      if (!$productId) {
        return null;
      }

      return Product::find($productId);
    }

    protected function createToastNotification(int $userId, string $heading, string $message, string $icon = 'success'): void
    {
      $payload = json_encode([
        'heading' => $heading,
        'message' => $message,
        'icon' => $icon,
      ]);

      UserNotification::create([
        'user_id' => $userId,
        'type' => $icon === 'error' ? 'danger' : ($icon === 'info' ? 'info' : 'success'),
        'message' => $payload,
        'show' => 1,
        'closable' => 1,
        'group' => 'toast',
      ]);
    }

    protected function handleRecurringDonationInvoiceSucceeded($invoice, $stripeSubscription): void
    {
      $meta = $stripeSubscription->metadata ?? null;
      $meta = $meta instanceof \Stripe\StripeObject ? $meta->toArray() : (array) $meta;

      $sellerId = isset($meta['seller_id']) ? (int) $meta['seller_id'] : null;
      $donorId = isset($meta['donor_id']) ? (int) $meta['donor_id'] : null;
      if (!$sellerId || !$donorId) {
        return;
      }

      $seller = User::find($sellerId);
      $donor = User::find($donorId);
      if (!$seller || !$donor) {
        return;
      }

      $paymentIntentId = $invoice->payment_intent ?? null;
      if ($paymentIntentId && Payments::where('stripe_id', $paymentIntentId)->exists()) {
        return;
      }

      $donationAmount = (float) ($meta['donation_amount'] ?? 0);
      $gross = isset($invoice->amount_paid) ? ((float) $invoice->amount_paid / 100) : $donationAmount;

      $coverFees = ($meta['cover_fees'] ?? '0') === '1';
      $anonymous = ($meta['anonymous'] ?? '0') === '1';
      $message = (string) ($meta['message'] ?? '');

      $platformPercent = $seller->options?->getFee() ?? 5.0;
      $platformFee = round($donationAmount * ($platformPercent / 100), 2);

      $stripeFee = 0.0;
      try {
        if ($paymentIntentId) {
          $pi = Cashier::stripe()->paymentIntents->retrieve($paymentIntentId);
          if (!empty($pi->latest_charge)) {
            $charge = Cashier::stripe()->charges->retrieve($pi->latest_charge, ['expand' => ['balance_transaction']]);
            $stripeFee = ($charge->balance_transaction->fee ?? 0) / 100;
          }
        }
      } catch (\Throwable $e) {
        // ignore
      }

      $authorAmount = $coverFees
        ? round(max(0, $donationAmount - $platformFee), 2)
        : round(max(0, $donationAmount - $platformFee - $stripeFee), 2);

      $userFund = UserFunds::create([
        'user_id' => $seller->id,
        'group' => 'donation',
        'type' => 'credit',
        'sum' => $authorAmount,
        'message' => $message ? Str::limit(strip_tags($message), 500) : '',
        'model' => $anonymous ? null : User::class,
        'model_id' => $anonymous ? null : $donor->id,
      ]);

      if ($paymentIntentId) {
        Payments::create([
          'user_id' => $donor->id,
          'amount' => $gross,
          'stripe_id' => $paymentIntentId,
          'status' => 'succeeded',
          'paymentable_type' => UserFunds::class,
          'paymentable_id' => $userFund->id,
        ]);
      }

      RevenueShare::create([
        'user_id' => $donor->id,
        'author_id' => $seller->id,
        'subscription_id' => $stripeSubscription->id ?? null,
        'amount_paid' => $gross,
        'stripe_fee' => $stripeFee,
        'net_amount' => $gross - $stripeFee,
        'author_amount' => $authorAmount,
        'service_amount' => $platformFee,
        'paid_at' => Carbon::now(),
      ]);

      // IV.email.3 (TЗ): recurring donation payment receipt (cycles only)
      if (($invoice->billing_reason ?? null) === 'subscription_cycle') {
        $periodStart = isset($invoice->period_start) ? Carbon::createFromTimestamp((int) $invoice->period_start) : null;
        $periodEnd = isset($invoice->period_end) ? Carbon::createFromTimestamp((int) $invoice->period_end) : null;
        $currentPeriodLabel = ($periodStart && $periodEnd)
          ? ($periodStart->format('m.d.Y') . ' - ' . $periodEnd->format('m.d.Y'))
          : 'Current donation period';

        $nextPaymentDateLabel = $periodEnd ? $periodEnd->format('m.d.Y') : null;

        try {
          Mail::to($donor->email)->send(new RecurringDonationPaymentReceipt(
            donor: $donor,
            seller: $seller,
            amountPaidLabel: '$' . number_format($gross, 2),
            currentPeriodLabel: $currentPeriodLabel,
            nextPaymentDateLabel: $nextPaymentDateLabel,
          ));
        } catch (\Throwable $e) {
          Log::warning('Failed to send recurring donation receipt email', [
            'stripe_subscription' => $stripeSubscription->id ?? null,
            'error' => $e->getMessage(),
          ]);
        }
      }
    }

    protected function handleRecurringDonationInvoiceFailed($invoice, $stripeSubscription): void
    {
      $meta = $stripeSubscription->metadata ?? null;
      $meta = $meta instanceof \Stripe\StripeObject ? $meta->toArray() : (array) $meta;

      $sellerId = isset($meta['seller_id']) ? (int) $meta['seller_id'] : null;
      $donorId = isset($meta['donor_id']) ? (int) $meta['donor_id'] : null;
      if (!$sellerId || !$donorId) {
        return;
      }

      $seller = User::find($sellerId);
      $donor = User::find($donorId);
      if (!$seller || !$donor) {
        return;
      }

      // IV.email.4 (TЗ): payment issue recurring donation
      try {
        Mail::to($donor->email)->send(new RecurringDonationPaymentIssue($donor, $seller));
      } catch (\Throwable $e) {
        Log::warning('Failed to send recurring donation payment issue email', [
          'stripe_subscription' => $stripeSubscription->id ?? null,
          'error' => $e->getMessage(),
        ]);
      }
    }

    protected function handleRecurringDonationSubscriptionUpdated($stripeSubscription, $previousAttributes = null): void
    {
      $meta = $stripeSubscription->metadata ?? null;
      $meta = $meta instanceof \Stripe\StripeObject ? $meta->toArray() : (array) $meta;

      $sellerId = isset($meta['seller_id']) ? (int) $meta['seller_id'] : null;
      $donorId = isset($meta['donor_id']) ? (int) $meta['donor_id'] : null;
      if (!$sellerId || !$donorId) {
        return;
      }

      $seller = User::find($sellerId);
      $donor = User::find($donorId);
      if (!$seller || !$donor) {
        return;
      }

      $cancelAtPeriodEnd = (bool) ($stripeSubscription->cancel_at_period_end ?? false);
      $prevCancelAtPeriodEnd = null;
      if (is_object($previousAttributes)) {
        $prevCancelAtPeriodEnd = $previousAttributes->cancel_at_period_end ?? null;
      } elseif (is_array($previousAttributes)) {
        $prevCancelAtPeriodEnd = $previousAttributes['cancel_at_period_end'] ?? null;
      }

      if ($cancelAtPeriodEnd && $prevCancelAtPeriodEnd === false) {
        // IV.email.5 (TЗ): recurring donation canceled (donor)
        try {
          Mail::to($donor->email)->send(new RecurringDonationCanceled($donor, $seller));
        } catch (\Throwable $e) {
          Log::warning('Failed to send recurring donation canceled email', [
            'stripe_subscription' => $stripeSubscription->id ?? null,
            'error' => $e->getMessage(),
          ]);
        }

        // IV.toast.8 (TЗ): Recurring Donation Canceled (seller)
        $this->createToastNotification(
          $seller->id,
          'Recurring Donation Canceled',
          'A user has canceled their recurring donation.',
          'info',
        );
      }
    }

    protected function handleRecurringDonationSubscriptionDeleted($stripeSubscription): void
    {
      $meta = $stripeSubscription->metadata ?? null;
      $meta = $meta instanceof \Stripe\StripeObject ? $meta->toArray() : (array) $meta;

      $sellerId = isset($meta['seller_id']) ? (int) $meta['seller_id'] : null;
      $donorId = isset($meta['donor_id']) ? (int) $meta['donor_id'] : null;
      if (!$sellerId || !$donorId) {
        return;
      }

      $seller = User::find($sellerId);
      $donor = User::find($donorId);
      if (!$seller || !$donor) {
        return;
      }

      try {
        Mail::to($donor->email)->send(new RecurringDonationCanceled($donor, $seller));
      } catch (\Throwable $e) {
        Log::warning('Failed to send recurring donation canceled email (deleted)', [
          'stripe_subscription' => $stripeSubscription->id ?? null,
          'error' => $e->getMessage(),
        ]);
      }

      $this->createToastNotification(
        $seller->id,
        'Recurring Donation Canceled',
        'A user has canceled their recurring donation.',
        'info',
      );
    }

    protected function handleStripePayoutEvent($stripePayout, string $eventType): void
    {
      $stripePayoutId = $stripePayout->id ?? null;
      if (!$stripePayoutId) {
        return;
      }

      // Sync local payout status if exists
      try {
        app(StripePayoutProcessor::class)->updateStatusFromStripe($stripePayoutId, (string) ($stripePayout->status ?? ''));
      } catch (\Throwable $e) {
        // ignore
      }

      // Sync withdrawal status too (if stripe_payout_id belongs to a withdrawal)
      try {
        $failureMessage = (string) ($stripePayout->failure_message ?? $stripePayout->failure_reason ?? '');
        app(StripeWithdrawalProcessor::class)->updateStatusFromStripe(
          $stripePayoutId,
          (string) ($stripePayout->status ?? ''),
          $failureMessage !== '' ? $failureMessage : null,
        );
      } catch (\Throwable $e) {
        // ignore
      }

      // Try to find local payout (for email gating & recipient)
      $localPayout = Payout::where('stripe_payout_id', $stripePayoutId)->first();
      $userId = $localPayout?->user_id ?? (isset($stripePayout->metadata['user_id']) ? (int) $stripePayout->metadata['user_id'] : null);
      if (!$userId) {
        return;
      }

      $user = User::find($userId);
      if (!$user) {
        return;
      }

      $amount = isset($stripePayout->amount) ? ((float) $stripePayout->amount / 100) : (float) ($localPayout?->amount ?? 0);
      $amountLabel = '$' . number_format($amount, 2);

      $arrival = isset($stripePayout->arrival_date) && $stripePayout->arrival_date
        ? Carbon::createFromTimestamp((int) $stripePayout->arrival_date)->format('m.d.Y')
        : null;

      // VII.email.2 (TЗ): payout created/in_transit
      if (in_array($eventType, ['payout.created', 'payout.updated'], true) && in_array(($stripePayout->status ?? null), ['pending', 'in_transit'], true)) {
        $key = 'email_payout_on_its_way_' . $stripePayoutId;
        if (!$user->notifications()->where('group', $key)->exists()) {
          try {
            Mail::to($user->email)->send(new PayoutOnItsWay($user, $amountLabel, $arrival));
            $user->notifications()->create([
              'type' => 'info',
              'group' => $key,
              'message' => json_encode(['sent_at' => now()->toDateTimeString()]),
              'show' => 0,
              'closable' => 0,
            ]);
          } catch (\Throwable $e) {
            Log::warning('Failed to send payout on its way email', [
              'user_id' => $user->id,
              'stripe_payout_id' => $stripePayoutId,
              'error' => $e->getMessage(),
            ]);
          }
        }
      }

      // VII.email.3 (TЗ): payout failed
      if ($eventType === 'payout.failed' || ($stripePayout->status ?? null) === 'failed') {
        $key = 'email_payout_failed_' . $stripePayoutId;
        if (!$user->notifications()->where('group', $key)->exists()) {
          $reason = (string) ($stripePayout->failure_message ?? $stripePayout->failure_reason ?? 'Payout failed');
          try {
            Mail::to($user->email)->send(new PayoutIssue($user, $reason, route('profile.settings')));
            $user->notifications()->create([
              'type' => 'info',
              'group' => $key,
              'message' => json_encode(['sent_at' => now()->toDateTimeString(), 'reason' => $reason]),
              'show' => 0,
              'closable' => 0,
            ]);
          } catch (\Throwable $e) {
            Log::warning('Failed to send payout failed email', [
              'user_id' => $user->id,
              'stripe_payout_id' => $stripePayoutId,
              'error' => $e->getMessage(),
            ]);
          }
        }
      }
    }
}
