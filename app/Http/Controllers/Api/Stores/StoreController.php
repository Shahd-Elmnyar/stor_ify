<?php

namespace App\Http\Controllers\Api\Stores;

use Exception;
use App\Models\Store;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\StoreResource;
use App\Http\Resources\BranchResource;
use App\Http\Controllers\AppController;
use App\Http\Resources\ProductResource;
use App\Http\Resources\CategoryDetailResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class StoreController extends AppController
{
    public function getStores($categoryID = null)
    {
        try {

            $query = Store::query();

            if ($categoryID) {
                $query->whereHas('categories', function ($q) use ($categoryID) {
                    $q->where('category_id', $categoryID);
                });
            }

            $stores = $query->with('categories')->paginate(6);

            return $this->successResponse([
                'stores' => StoreResource::collection($stores),
                'pagination' => $this->getPaginationData($stores),
            ]);
        } catch (\Exception $e) {
        Log::error('Error : ' . $e->getMessage());

            return $this->genericErrorResponse();
        }
    }

    public function getProductsWithDiscount($storeId)
    {
        try {
            $products = Product::where('store_id', $storeId)
                ->whereNotNull('discount')
                ->with('store')
                ->paginate(6);
            if (!$products) {
                return $this->notFoundResponse('PRODUCTS_NOT_FOUND');
            }

            return $this->successResponse([
                'products' => ProductResource::collection($products),
                'pagination' => $this->getPaginationData($products),
            ]);
        } catch (Exception $e) {
            return $this->genericErrorResponse();
        }
    }
    public function getBranches($storeId)
    {
        try {
            $store = Store::findOrFail($storeId);
            $branches = $store->branches;

            return $this->successResponse(
                [
                    'branches' => BranchResource::collection($branches),
                ]
            );
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('BRANCH_NOT_FOUND');
        } catch (\Exception $e) {
            return $this->genericErrorResponse();
        }
    }

    public function getStoreCategories($storeId)
    {
        try {
            $store = Store::findOrFail($storeId);
            $categories = $store->categories->load('products', 'subCategories');

            return $this->successResponse([
                'categories' => CategoryDetailResource::collection($categories)
            ]);
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('CATEGORY_NOT_FOUND');
        } catch (\Exception $e) {
            Log::error('Error : ' . $e->getMessage());
            return $this->genericErrorResponse();
        }
    }
}
