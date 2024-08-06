<?php

namespace App\Http\Controllers\Api\Cart;

use Exception;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\AppController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CartController extends AppController
{

    public function addProductToCart(Request $request, $productId)
    {
        try {

            $userId = $this->user->id; // Extract the user ID

            // Validate request data
            $validator = Validator::make(
                $request->all(),
                [
                    'size_id' => 'required|exists:sizes,id',
                    'quantity' => 'required|integer|min:1',
                    'color_id' => 'required|exists:colors,id', // Ensure color_id is validated
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

            $data = $validator->validated(); // Retrieve the validated data

            // Find the product
            $product = $this->getProductById($productId);

            // Check if the size exists for the product
            $this->productSizeCheck($product, $data);

            // Create or find the cart and set the total_price
            $cart = Cart::firstOrCreate(
                ['user_id' => $userId], // Use the user ID
                ['total_price' => 0.0] // Ensure total_price is a float
            );

            // Check if the product already exists in the cart
            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $productId)
                ->where('size_id', $data['size_id'])
                ->where('color_id', $data['color_id'])
                ->first();

            if ($cartItem) {
                // Update the quantity and total if the item exists
                $cartItem->quantity += $data['quantity'];
                $cartItem->total = $cartItem->quantity * $product->price;
                $cartItem->save();
            } else {
                // Create and save CartItem if it doesn't exist
                $cartItem = CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $productId,
                    'price' => $product->price, // Ensure price is included
                    'size_id' => $data['size_id'],
                    'color_id' => $data['color_id'], // Ensure color_id is included
                    'quantity' => $data['quantity'],
                    'total' => ($data['quantity'] * $product->price), // Ensure total is calculated and included
                ]);
            }

            // Update the cart's total price
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

        $cart = Cart::where('user_id', $this->user->id)->with('cartItems.product')->first();

        if (!$cart) {
            return $this->successResponse([
                'cart' => []
            ]);
        }

        // Debugging: Log the cart items
        Log::info('Cart Items: ' . $cart->cartItems);

        $totalItems = $cart->cartItems->sum('quantity');
        $totalPrice = $cart->total_price;

        return $this->successResponse([
            'cart' => $cart->cartItems,
            'totalItems' => $totalItems,
            'totalPrice' => $totalPrice
        ]);
    }
    public function deleteProductFromCart(Request $request, $productId)
    {
        try {

            $userId = $this->user->id; // Extract the user ID


            // Find the cart
            $cart = Cart::where('user_id', $userId)->first();
            if (!$cart) {
                return $this->notFoundResponse('CART_IS_EMPTY');
            }

            // Find the cart item
            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $productId)
                ->first();

            if (!$cartItem) {
                return $this->notFoundResponse('CART_ITEM_NOT_iN_CART');
            }

            // Delete the cart item
            $cartItem->delete();

            // Update the cart's total price
            $this->updateTotalCartPrice($cart, $cartItem, true);

            // Check if the cart is empty and delete it if it is
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
