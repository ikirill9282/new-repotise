<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StripeWebhook
{
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next): Response
  {
    $signature = $request->header('Stripe-Signature');
    $payload = $request->getContent();
    $secret = env('STRIPE_WEBHOOK_SECRET');

    try {
        \Stripe\Webhook::constructEvent(
            $payload, $signature, $secret
        );
    } catch (\Exception $e) {
        return response('Unauthorized', 401);
    }
    return $next($request);
  }
}
