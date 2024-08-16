<?php

namespace App\Http\Controllers\Api\Checkout;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Payment;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\OrderResource;
use App\Http\Requests\CheckoutRequest;
use App\Http\Controllers\AppController;
use Illuminate\Support\Facades\Validator;


class CheckoutController extends AppController
{
    public function checkout(CheckoutRequest $request)
    {
        Log::info('Checkout request received', $request->all());
        $cart = Cart::where('user_id', $this->user->id)->first();

        if (!$cart || $cart->cartItems->isEmpty()) {
            return response()->json(['code' => 'CART_EMPTY'], 400);
        }

        $order = null;

            // Create the order
            $order = Order::create([
                'total' => $cart->cartItems->sum(fn ($item) => $item->quantity * $item->price),
                'status' => 'pending',
                'user_id' => auth()->id(),
                'delivery_date' => $request->date,
                'delivery_time' => $request->time,
            ]);

            foreach ($cart->cartItems as $item) {
                $orderItem = OrderItem::create([
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total' => $item->quantity * $item->price,
                    'product_id' => $item->product_id,
                    'order_id' => $order->id,
                    'size_id' => $item->size_id,
                    'color_id' => $item->color_id,
                ]);
            }

            // // Clear the cart
            // $cart->cartItems()->delete();
            // $cart->delete();

        return $this->successResponse(
            [
            'order' => new OrderResource($order),
            ]
        );
    }
    public function updatePaymentMethod(Request $request, $orderId)
    {
        // Log the request data for debugging
        // Log::info('Update payment method request received');
        // dd($request->all());
        $validator  = $validator = Validator::make(
            $request->all(), [
            'payment_method' => 'required|in:cash,card',
        ], [
            'payment_method.in' => 'INVALID_PAYMENT_METHOD',
            'payment_method.required' => 'PAYMENT_METHOD_REQUIRED',
        ]);
        // dd($validator->fails());
        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->first());
        }
        $cart = Cart::where('user_id', $this->user->id)->first();
        if (!$cart || $cart->cartItems->isEmpty()) {
            return response()->json(['code' => 'CART_EMPTY'], 400);
        }
        $order = Order::findOrFail($orderId);
        // Retrieve the payment ID from the payments table
        $payment = Payment::where('method', $request->payment_method)->first();
        if (!$payment) {
            return $this->notFoundResponse('PAYMENT_METHOD_NOT_FOUND');
        }
        $order->payment_id = $payment->id;
        $order->save();

        return $this->successResponse();
    }

}
