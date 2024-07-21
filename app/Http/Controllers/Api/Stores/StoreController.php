<?php

namespace App\Http\Controllers\Api\Stores;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Store;
use App\Http\Resources\StoreResource; // Import the StoreResource

class StoreController extends Controller
{
    public function getStores(Request $request, $categoryID = null) // Accept category ID as an optional parameter
    {
        try {
            $user = $this->getUser($request);
            if (!$user) {
                return $this->unauthorizedResponse();
            }
            $query = Store::query();

            if ($categoryID) {
                $query->where('category_id', $categoryID); // Filter by category ID if provided
            }

            $stores = $query->paginate(6); // Paginate the results with 6 stores per page

            // Return the response using the StoreResource
            return response()->json([
                'code' => 'SUCCESS',
                'data' => [
                    'stores' => StoreResource::collection($stores),
                    'pagination' => $this->getPaginationData($stores),
                ],
            ]);
        } catch (\Exception $e) {
            // Handle the exception and return an error response
            return $this->genericErrorResponse();
        }
    }

}
