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
    $secret = 'whsec_hwi48kvum7N4Q7noo2dMB5CjIG4Zy2Ae';

    try {
        $event = \Stripe\Webhook::constructEvent(
            $payload, $signature, $secret
        );
        var_dump($event);
    } catch (\Exception $e) {
        return response('Unauthorized', 401);
    }
    return $next($request);
  }
}
