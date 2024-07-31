<?php

namespace App\Http\Controllers\Favorite;

use Exception;
use App\Models\Store;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\StoreResource;


class StoreController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = $this->getUser($request);
            if (!$user) {
                return $this->unauthorizedResponse();
            }

            $favoriteStores = $this->getUserFavoriteStores($user->id);
            if ($favoriteStores->isEmpty()) {
                return $this->notFoundResponse('NO_FAVORITE_STORES');
            }

            return $this->successResponse(StoreResource::collection($favoriteStores));
        } catch (Exception $e) {
            Log::error('Error during forget password process: ' . $e->getMessage());

            return $this->genericErrorResponse();
        }
    }

    private function getUserFavoriteStores($userId)
    {
        return Favorite::with('store')
            ->where('user_id', $userId)
            ->get()
            ->pluck('store');
    }

    public function store(Request $request)
    {
        try {
            $user = $this->getUser($request);
            if (!$user) {
                return $this->unauthorizedResponse();
            }

            // Check if the store exists
            $storeExists = Store::where('id', $request->store_id)->exists();
            if (!$storeExists) {
                return $this->notFoundResponse('STORE_NOT_FOUND');
            }

            // Check if the store is already in favorites
            $alreadyFavorited = Favorite::where('user_id', $user->id)
                ->where('store_id', $request->store_id)
                ->exists();
            if ($alreadyFavorited) {
                return response()->json([
                    'code' => 'ERROR',
                    'data' => 'STORE_ALREADY_FAVORITED',
                ], 422);
            }

            $favorite = Favorite::firstOrCreate([
                'user_id' => $user->id,
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
            $user = $this->getUser($request);
            if (!$user) {
                return $this->unauthorizedResponse();
            }

            $favorite = Favorite::where('user_id', $user->id)
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
