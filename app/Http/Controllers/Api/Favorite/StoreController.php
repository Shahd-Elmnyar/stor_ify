<?php

namespace App\Http\Controllers\Api\Favorite;

use Exception;
use App\Models\Store;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\AppController;
use App\Http\Resources\StoreResource;


class StoreController extends AppController
{
    public function index(Request $request)
    {
        try {


            $favoriteStores = $this->getUserFavoriteStores();
            if ($favoriteStores->isEmpty()) {
                return $this->notFoundResponse('NO_FAVORITE_STORES');
            }

            return $this->successResponse(StoreResource::collection($favoriteStores));
        } catch (Exception $e) {
            Log::error('Error during forget password process: ' . $e->getMessage());

            return $this->genericErrorResponse();
        }
    }

    private function getUserFavoriteStores()
    {
        return Favorite::with('store')
            ->where('user_id', $this->user->id)
            ->get()
            ->pluck('store');
    }

    public function store(Request $request)
    {
        try {


            // Check if the store exists
            $storeExists = Store::where('id', $request->store_id)->exists();
            if (!$storeExists) {
                return $this->notFoundResponse('STORE_NOT_FOUND');
            }

            // Check if the store is already in favorites
            $alreadyFavorited = Favorite::where('user_id', $this->user->id)
                ->where('store_id', $request->store_id)
                ->exists();
            if ($alreadyFavorited) {
                return response()->json([
                    'code' => 'ERROR',
                    'data' => 'STORE_ALREADY_FAVORITED',
                ], 422);
            }

            $favorite = Favorite::firstOrCreate([
                'user_id' => $this->user->id,
                'store_id' => $request->store_id,
            ]);

            return $this->successResponse([new StoreResource($favorite->store)]);
        } catch (Exception $e) {
            return $this->genericErrorResponse();
        }
    }

    public function destroy(Request $request, $id)
    {
        try {


            $favorite = Favorite::where('user_id', $this->user->id)
                ->where('Store_id', $id)
                ->first();

            if ($favorite) {
                $favorite->delete();
                return $this->successResponse('STORE_REMOVED');
            } else {
                return $this->notFoundResponse('STORE_NOT_FAVORITED');
            }
        } catch (Exception $e) {
            return $this->genericErrorResponse();
        }
    }
}
