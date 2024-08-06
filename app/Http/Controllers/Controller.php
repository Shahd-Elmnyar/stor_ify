<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function checkAuthorization($request)
    {
        $user = $request->user();

        if (!$user) {
            return $this->unauthorizedResponse();
        }
        return $user;
    }
    public function unauthorizedResponse()
    {
        return response()->json([
            'code' => 'USER_NOT_AUTH',
        ], 401);
    }

    public function notFoundResponse($message)
    {
        return response()->json([
            'code' => $message,
        ], 404);
    }

    public function genericErrorResponse()
    {
        return response()->json([
            'code' =>'GENERIC_ERROR',
        ], 500);
    }
    public function validationErrorResponse($message)
    {
        return response()->json([
            'code' => $message,
        ], 403);
    }

    public function successResponse($data= " ")
    {
        return response()->json(
            $data, 200);
    }

    public function getCategories($perPage)
    {
        return Category::with('subCategories')->paginate($perPage);
    }
    public function getCategoryById($id)
    {
        return Category::with('subCategories')->findOrFail($id);
    }

    public function getCategoryProducts(Category $category, $subCategoryId)
    {
        $query = $category->products()->with('images', 'subCategory', 'colors', 'sizes','store');

        if ($subCategoryId) {
            $query->where('sub_category_id', $subCategoryId);
        }

        return $query->paginate(6);
    }

    public function subcategoryExists(Category $category, $subCategoryId)
    {
        return $category->subCategories()->where('id', $subCategoryId)->exists();
    }

    public function getPaginationData($products)
    {
        if ($products instanceof LengthAwarePaginator) {
            return [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'next_page_url' => $products->nextPageUrl(),
                'prev_page_url' => $products->previousPageUrl(),
            ];
        }
        return [];
    }
    public function updateTotalCartPrice($cart, $cartItem = null, $isDeleting = false)
    {
        // Ensure that $cart->total_price is numeric
        if (!is_numeric($cart->total_price)) {
            Log::error('Non-numeric value encountered in updateTotalCartPrice', [
                'cart_total_price' => $cart->total_price,
            ]);
            throw new \Exception('Non-numeric value encountered in updateTotalCartPrice');
        }

        if ($cartItem) {
            // Ensure that $cartItem->total is numeric
            if (!is_numeric($cartItem->total)) {
                Log::error('Non-numeric value encountered in updateTotalCartPrice', [
                    'cart_item_total' => $cartItem->total
                ]);
                throw new \Exception('Non-numeric value encountered in updateTotalCartPrice');
            }

            if ($isDeleting) {
                $cart->total_price -= $cartItem->total;
            } else {
                $cart->total_price += $cartItem->total;
            }
        }

        // Check if the cart is empty and set total_price to 0 if it is
        if ($cart->cartItems->isEmpty()) {
            $cart->total_price = 0.00;
        }

        $cart->save();
    }
    public function getProductById($productId)
    {
        return Product::findOrFail($productId);
    }
    public function productSizeCheck($product, $data)
    {
        $sizeExists = $product->sizes()->where('sizes.id', $data['size_id'])->exists();
        if (!$sizeExists) {
            return $this->validationErrorResponse(
                'SIZE_ID_NOT_FOUND_FOR_PRODUCT'
            );
        }
    }

    public function productColorCheck($product, $data)
    {
        $colorExists = $product->colors()->where('colors.id', $data['color_id'])->exists();
        if (!$colorExists) {
            return $this->validationErrorResponse(
                'COLOR_ID_NOT_FOUND_FOR_PRODUCT'
            );
        }
    }

}
