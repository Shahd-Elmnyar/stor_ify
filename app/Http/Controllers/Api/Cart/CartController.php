<?php

namespace App\Http\Controllers\Api\Cart;

use Exception;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\AppController;
use App\Http\Resources\CartItemResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CartController extends AppController
{
    public function addProductToCart(Request $request, $productId)
    {
        try {
            $userId = $this->user->id;

            $validator = Validator::make(
                $request->all(),
                [
                    'size_id' => 'required|exists:sizes,id',
                    'quantity' => 'required|integer|min:1',
                    'color_id' => 'required|exists:colors,id',
                ],
                [
                    'size_id.required' => 'SIZE_REQUIRED',
                    'quantity.required' => 'QUANTITY_REQUIRED',
                    'color_id.required' => 'COLOR_REQUIRED',
                    'quantity.min' => 'QUANTITY_MIN',
                    'color_id.exists' => 'COLOR_NOT_FOUND_FOR_PRODUCT',
                    'size_id.exists' => 'SIZE_NOT_FOUND_FOR_PRODUCT',
                ]
            );

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors()->first());
            }

            $data = $validator->validated();

            $product = $this->getProductById($productId);
            $this->productSizeCheck($product, $data);

            $cart = Cart::firstOrCreate(
                ['user_id' => $userId],
                ['total_price' => 0.0]
            );

            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $productId)
                ->where('size_id', $data['size_id'])
                ->where('color_id', $data['color_id'])
                ->first();

            if ($cartItem) {
                $cartItem->quantity += $data['quantity'];
                $cartItem->total = $cartItem->quantity * $product->price;
                $cartItem->save();
            } else {
                $cartItem = CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $productId,
                    'price' => $product->price,
                    'size_id' => $data['size_id'],
                    'color_id' => $data['color_id'],
                    'quantity' => $data['quantity'],
                    'total' => $data['quantity'] * $product->price,
                ]);
            }

            $this->updateTotalCartPrice($cart, $cartItem);

            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('NO_PRODUCT_FOUND');
        } catch (Exception $e) {
            Log::error('General error: ', ['error' => $e->getMessage()]);
            return $this->genericErrorResponse();
        }
    }

    public function showCart(Request $request)
    {
        $cart = Cart::where('user_id', $this->user->id)
            ->with('cartItems.product', 'cartItems.product.colors', 'cartItems.product.sizes','cartItems.product.images')
            ->first();

        if (!$cart) {
            return $this->successResponse([
                'cart' => [],
                'totalItems' => 0,
                'subTotalPrice'=>0,
                'delivery'=>0,
                'totalPrice' => 0.0
            ]);
        }

        $totalItems = $cart->cartItems->sum('quantity');
        $subtotalPrice = $cart->total_price;
        $delivery = 20 ;
        $totalPrice = $subtotalPrice + $delivery;

        return $this->successResponse([
            'cart' => CartItemResource::collection($cart->cartItems),
            'totalItems' => $totalItems,
            'subTotalPrice' => $subtotalPrice,
            'delivery' => $delivery,
            'totalPrice' => $totalPrice
        ]);
    }

    public function deleteProductFromCart(Request $request, $productId)
    {
        try {
            $userId = $this->user->id;

            $cart = Cart::where('user_id', $userId)->first();
            if (!$cart) {
                return $this->notFoundResponse('CART_IS_EMPTY');
            }

            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $productId)
                ->first();

            if (!$cartItem) {
                return $this->notFoundResponse('CART_ITEM_NOT_IN_CART');
            }

            $cartItem->delete();
            $this->updateTotalCartPrice($cart, $cartItem, true);

            if ($cart->cartItems->isEmpty()) {
                $cart->delete();
            }

            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('NO_PRODUCT_FOUND');
        } catch (Exception $e) {
            Log::error('General error: ', ['error' => $e->getMessage()]);
            return $this->genericErrorResponse();
        }
    }
}
