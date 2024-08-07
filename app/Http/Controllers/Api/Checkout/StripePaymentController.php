<?php

namespace App\Http\Controllers\APi\Checkout;

use App\Models\Cart;
use App\Models\Order;
use Stripe\StripeClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AppController;

class StripePaymentController extends AppController
{
    public function stripePost(Request $request, $orderId)
{
    // Log the incoming request for debugging
    Log::info('Incoming Request: ', $request->all());

    // Validate the request
    $request->validate([
        'number' => 'required|string',
        'exp_month' => 'required|integer|between:1,12',
        'exp_year' => 'required|integer|min:' . date('Y'),
        'amount' => 'required|integer|min:2500',
        'cvc' => 'required|integer',
    ], [
        'number.required' => 'NUMBER_REQUIRED',
        'number.string' => 'NUMBER_STRING',
        'exp_month.required' => 'EXP_MONTH_REQUIRED',
        'exp_month.integer' => 'EXP_MONTH_INTEGER',
        'exp_month.between' => 'EXP_MONTH_BETWEEN',
        'exp_year.required' => 'EXP_YEAR_REQUIRED',
        'exp_year.integer' => 'EXP_YEAR_INTEGER',
        'exp_year.min' => 'EXP_YEAR_MIN',
        'amount.required' => 'AMOUNT_REQUIRED',
        'amount.integer' => 'AMOUNT_INTEGER',
        'amount.min' => 'MIN_2500',
        'cvc.required' => 'CVC_REQUIRED',
        'cvc.integer' => 'CVC_INTEGER',
    ]);

    try {
        $stripe = new StripeClient(env('STRIPE_SECRET'));

        // Use a test token instead of card details
        $testToken = 'tok_visa'; // This is a predefined test token

        // Convert the amount to an integer
        $amount = intval($request->amount);

        // Use the token to create a charge
        $response = $stripe->charges->create([
            'amount' => $amount,
            'currency' => 'egp',
            'source' => $testToken,
        ]);

        $cart = Cart::where('user_id', $this->user->id)->first();
            if (!$cart || $cart->cartItems->isEmpty()) {
                return response()->json(['code' => 'CART_EMPTY'], 400);
            }
        $cart->cartItems()->delete();
        $cart->delete();

        $order = Order::findOrFail($orderId);
        $order->status = 'processing';
        $order->save();

        return $this->successResponse();
    } catch (\Exception $e) {
        // Log the error for debugging
        Log::error('Stripe Payment Error: ' . $e->getMessage());

        return $this->genericErrorResponse();
    }
}
}
