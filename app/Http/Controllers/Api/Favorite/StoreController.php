<?php

namespace App\Http\Controllers\Api\Favorite;

use Exception;
use App\Models\Store;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\AppController;
use App\Http\Resources\StoreResource;

class StoreController extends AppController{
public function index(Request $request)
{
        $favoriteStores = $this->getUserFavoriteStores();
        if ($favoriteStores->isEmpty()) {
            $data = ['stores' =>[]];
        }
        else{
            $data =['stores' => StoreResource::collection($favoriteStores)];
        }
        return $this->successResponse($data);
}


    private function getUserFavoriteStores()
    {
        return Favorite::with('store')
            ->where('user_id', $this->user->id)->with('store.categories')
            ->get()
            ->pluck('store')
            ->filter();
    }

    public function store(Request $request)
    {
        try {


            $storeExists = Store::where('id', $request->store_id)->exists();
            if (!$storeExists) {
                return $this->notFoundResponse('STORE_NOT_FOUND');
            }


            $alreadyFavorited = Favorite::where('user_id', $this->user->id)
                ->where('store_id', $request->store_id)
                ->exists();
            if ($alreadyFavorited) {
                return response()->json([
                    'code' => 'STORE_ALREADY_FAVORITED',
                ], 422);
            }

            $favorite = Favorite::firstOrCreate([
                'user_id' => $this->user->id,
                'store_id' => $request->store_id,
            ]);

            return $this->successResponse();
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
                return $this->successResponse();
            } else {
                return $this->notFoundResponse('STORE_NOT_FAVORITED');
            }
        } catch (Exception $e) {
            return $this->genericErrorResponse();
        }
    }
}
