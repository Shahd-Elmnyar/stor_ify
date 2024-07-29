<?php

namespace App\Http\Controllers\Api\Stores;

use Exception;
use App\Models\Store;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\BranchResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\CategoryDetailResource;
use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Resources\StoreResource;

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
                // Use a join to filter stores by category ID
                $query->whereHas('categories', function ($q) use ($categoryID) {
                    $q->where('category_id', $categoryID); // Filter by category ID
                });
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

    public function getProductsWithDiscount(Request $request, $storeId) // New function to get products with discount
    {
        try {
            $user = $this->getUser($request);
            if (!$user) {
                return $this->unauthorizedResponse();
            }

            $products = Product::where('store_id', $storeId)
                ->whereNotNull('discount')
                ->with('store')
                ->paginate(6);

            return response()->json([
                'code' => 'SUCCESS',
                'data' => [
                    'products' => ProductResource::collection($products),
                    'pagination' => $this->getPaginationData($products),
                ],

            ]);
        } catch (Exception $e) {
            return $this->genericErrorResponse();
        }
    }
    public function getBranches(Request $request, $storeId)
    {
        try {
            $user = $this->getUser($request);
            if (!$user) {
                return $this->unauthorizedResponse();
            }

            $store = Store::findOrFail($storeId);
            $branches = $store->branches;

            return response()->json([
                'code' => 'SUCCESS',
                'data' => [
                    'branches' => BranchResource::collection($branches),
                ]
            ]);
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('BRANCH_NOT_FOUND');
        } catch (\Exception $e) {
            return $this->genericErrorResponse();
        }
    }

    public function getStoreCategories(Request $request, $storeId)
    {
        try {
            $user = $this->getUser($request);
            if (!$user) {
                return $this->unauthorizedResponse();
            }

            $store = Store::findOrFail($storeId);
            $categories = $store->categories;

            return response()->json([
                'code' => 'SUCCESS',
                'data' => [
                    'categories' => CategoryDetailResource::collection($categories),
                ]
            ]);
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('CATEGORY_NOT_FOUND');
        } catch (\Exception $e) {
            return $this->genericErrorResponse();
        }
    }
}

