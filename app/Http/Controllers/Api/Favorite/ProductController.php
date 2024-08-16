<?php

namespace App\Http\Controllers\Api\Favorite;

use Exception;
use App\Models\Cart;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\AppController;
use App\Http\Resources\ProductResource;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductController extends AppController
{
    public function index()
    {
        try {
            $favoriteProducts = $this->getUserFavoriteProducts();
            if ($favoriteProducts->isEmpty()) {
                return $this->notFoundResponse('NO_FAVORITE_PRODUCTS');
            }
            // dd(ProductResource::collection($favoriteProducts));

            return $this->successResponse(['products'=>ProductResource::collection($favoriteProducts)]);
        } catch (Exception $e) {
            Log::error('Error during get favorite products process: ' . $e->getMessage());

            return $this->genericErrorResponse();
        }
    }
    private function getUserFavoriteProducts()
    {
        return Favorite::with('product')
            ->where('user_id', $this->user->id)
            ->get()
            ->pluck('product')
            ->filter();
    }

    public function store(Request $request)
    {
        try {
            // Check if the product exists
            $productExists = Product::where('id', $request->product_id)->exists();
            if (!$productExists) {
                return $this->notFoundResponse('PRODUCT_NOT_FOUND');
            }

            // Check if the product is already in favorites
            $alreadyFavorited = Favorite::where('user_id', $this->user->id)
                ->where('product_id', $request->product_id)
                ->exists();
            if ($alreadyFavorited) {
                return response()->json([
                    'code'=> 'PRODUCT_ALREADY_FAVORITED',
                ], 422);
            }

            $favorite = Favorite::firstOrCreate([
                'user_id' => $this->user->id,
                'product_id' => $request->product_id,
            ]);

            return $this->successResponse();
        } catch (Exception $e) {
            return $this->genericErrorResponse();
        }
    }

    public function destroy( $id)
    {
        try {
            $favorite = Favorite::where('user_id', $this->user->id)
                ->where('product_id', $id)
                ->first();

            if ($favorite) {
                $favorite->delete();
                return $this->successResponse();
            } else {
                return $this->notFoundResponse('PRODUCT_NOT_FAVORITED');
            }
        } catch (Exception $e) {
            return $this->genericErrorResponse();
        }
    }
}
