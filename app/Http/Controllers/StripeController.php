<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StripeController extends Controller
{
    public function hook(Request $request)
    {
      Log::channel('stripe_events')->debug('Stripe Event', ['data' => $request->attributes->get('stripe_event')]);
      return response('ok');
    }
}
