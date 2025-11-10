<?php

namespace App\Services;

use App\Models\RefundRequest;
use App\Models\RevenueShare;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Laravel\Cashier\Cashier;
use Stripe\Exception\ApiErrorException;

class StripeRefundProcessor
{
    public function process(RefundRequest $refundRequest): RefundRequest
    {
        if ($refundRequest->status !== 'pending') {
            throw new RefundProcessingException('This refund request has already been resolved.');
        }

        $refundRequest->loadMissing([
            'order.payments',
            'orderProduct.product',
        ]);

        $order = $refundRequest->order;
        $orderProduct = $refundRequest->orderProduct;

        if (!$order || !$orderProduct) {
            throw new RefundProcessingException('Refundable order item was not found.');
        }

        if ((bool) $orderProduct->refunded) {
            throw new RefundProcessingException('This item has already been refunded.');
        }

        $payment = $order->getSuccessPayment();

        if (!$payment) {
            throw new RefundProcessingException('No settled payment is available for this order.');
        }

        try {
            $paymentIntent = $payment->asStripePaymentIntent();
        } catch (\Throwable $e) {
            throw new RefundProcessingException('Unable to retrieve payment details from Stripe.', 0, $e);
        }

        if (!$paymentIntent || empty($paymentIntent->id)) {
            throw new RefundProcessingException('Payment intent is missing on Stripe.');
        }

        $amount = (float) $orderProduct->total;
        if ($amount <= 0) {
            throw new RefundProcessingException('Refund amount must be greater than zero.');
        }

        $amountInCents = (int) round($amount * 100);
        $amountReceived = (int) ($paymentIntent->amount_received ?? $paymentIntent->amount ?? 0);
        $amountAlreadyRefunded = (int) ($paymentIntent->amount_refunded ?? 0);
        $remainingRefundable = max(0, $amountReceived - $amountAlreadyRefunded);

        if ($remainingRefundable <= 0) {
            throw new RefundProcessingException('The payment has already been fully refunded.');
        }

        if ($amountInCents > $remainingRefundable) {
            $amountInCents = $remainingRefundable;
        }

        try {
            $stripeRefund = Cashier::stripe()->refunds->create([
                'payment_intent' => $paymentIntent->id,
                'amount' => $amountInCents,
                'metadata' => [
                    'refund_request_id' => $refundRequest->id,
                    'order_id' => $order->id,
                    'order_product_id' => $orderProduct->id,
                ],
            ]);
        } catch (ApiErrorException $e) {
            throw new RefundProcessingException($e->getMessage(), 0, $e);
        }

        $refundedAmount = $amountInCents / 100;

        return DB::transaction(function () use ($refundRequest, $orderProduct, $stripeRefund, $refundedAmount) {
            $currency = strtoupper($stripeRefund->currency ?? 'USD');

            $refundRequest->forceFill([
                'status' => 'approved',
                'resolved_at' => now(),
                'stripe_refund_id' => $stripeRefund->id,
                'stripe_refund_status' => $stripeRefund->status ?? null,
                'refund_amount' => $refundedAmount,
                'refund_currency' => $currency,
                'stripe_refund_error' => null,
            ])->save();

            $orderProduct->update(['refunded' => 1]);

            $this->reverseRevenueShare($refundRequest);

            return $refundRequest->refresh();
        });
    }

    protected function reverseRevenueShare(RefundRequest $refundRequest): void
    {
        $share = RevenueShare::query()
            ->where('order_id', $refundRequest->order_id)
            ->where('product_id', optional($refundRequest->orderProduct)->product_id)
            ->whereNull('refunded_at')
            ->first();

        if (!$share) {
            return;
        }

        if ($share->author && $share->author_amount > 0) {
            $share->author->decrement('balance', $share->author_amount);
        }

        if ($share->referrer_id && $share->referral_amount > 0) {
            User::whereKey($share->referrer_id)->decrement('balance', $share->referral_amount);
        }

        $systemUser = User::find(0);
        if ($systemUser && $share->service_amount > 0) {
            $systemUser->decrement('balance', $share->service_amount);
        }

        $share->forceFill([
            'refund_request_id' => $refundRequest->id,
            'refunded_at' => now(),
        ])->save();
    }
}
