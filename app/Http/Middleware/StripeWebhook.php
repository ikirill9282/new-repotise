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
    try {
      $event = \Stripe\Webhook::constructEvent(
        $request->body(),
        $request->header('stripe-signature'),
        env('STRIPE_WEBHOOK_SECRET'),
      );
      var_dump($event);
    } catch (\UnexpectedValueException $e) {
      var_dump($e);
      http_response_code(400);
      exit();
    } catch (\Stripe\Exception\SignatureVerificationException $e) {
      // Invalid signature
      var_dump($e);
      http_response_code(400);
      exit();
    }
    return $next($request);
  }
}
