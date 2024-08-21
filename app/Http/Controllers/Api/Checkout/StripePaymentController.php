<?php

namespace App\Http\Controllers\APi\Checkout;

use App\Models\Cart;
use App\Models\Order;
use Stripe\StripeClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\AppController;

class StripePaymentController extends AppController
{
    public function stripePost(Request $request, $orderId)
{

    Log::info('Incoming Request: ', $request->all());


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


        $testToken = 'tok_visa';


        $amount = intval($request->amount);


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

        Log::error('Stripe Payment Error: ' . $e->getMessage());

        return $this->genericErrorResponse();
    }
}
}
