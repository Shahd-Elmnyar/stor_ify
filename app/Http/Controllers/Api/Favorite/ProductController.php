<?php

namespace App\Http\Controllers\Api\Favorite;

use Exception;
use App\Models\Product;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = $this->getUser($request);
            if (!$user) {
                return $this->unauthorizedResponse();
            }


            $favoriteProducts = $this->getUserFavoriteProducts($user->id);
            if ($favoriteProducts->isEmpty()) {
                return $this->notFoundResponse('NO_FAVORITE_PRODUCTS');
            }
            // dd(ProductResource::collection($favoriteProducts));

            return $this->successResponse(ProductResource::collection($favoriteProducts));
        } catch (Exception $e) {
            Log::error('Error during get favorite products process: ' . $e->getMessage());

            return $this->genericErrorResponse();
        }
    }
    private function getUserFavoriteProducts($userId)
    {
        return Favorite::with('product')
            ->where('user_id', $userId)
            ->get()
            ->pluck('product')
            ->filter();
    }

    public function store(Request $request)
    {
        try {
            $user = $this->getUser($request);
            if (!$user) {
                return $this->unauthorizedResponse();
            }

            // Check if the product exists
            $productExists = Product::where('id', $request->product_id)->exists();
            if (!$productExists) {
                return $this->notFoundResponse('PRODUCT_NOT_FOUND');
            }

            // Check if the product is already in favorites
            $alreadyFavorited = Favorite::where('user_id', $user->id)
                ->where('product_id', $request->product_id)
                ->exists();
            if ($alreadyFavorited) {
                return response()->json([
                    'code' => 'ERROR',
                    'data' => 'PRODUCT_ALREADY_FAVORITED',
                ], 422);
            }

            $favorite = Favorite::firstOrCreate([
                'user_id' => $user->id,
                'product_id' => $request->product_id,
            ]);

            return $this->successResponse([new ProductResource($favorite->product)]);
        } catch (Exception $e) {
            return $this->genericErrorResponse();
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $user = $this->getUser($request);
            if (!$user) {
                return $this->unauthorizedResponse();
            }

            $favorite = Favorite::where('user_id', $user->id)
                ->where('product_id', $id)
                ->first();

            if ($favorite) {
                $favorite->delete();
                return $this->successResponse('PRODUCT_REMOVED');
            } else {
                return $this->notFoundResponse('PRODUCT_NOT_FAVORITED');
            }
        } catch (Exception $e) {
            return $this->genericErrorResponse();
        }
    }
}
