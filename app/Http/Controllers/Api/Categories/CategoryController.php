<?php

namespace App\Http\Controllers\Api\Categories;

use Exception;
use App\Models\Category;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\AppController;
use App\Http\Resources\ProductResource;
use App\Http\Resources\CategoryHomeResource;
use App\Http\Resources\CategoryDetailResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoryController extends AppController
{
    public function index()
    {


        try {
            $categories = $this->getCategories(8);
            return $this->successResponse([
                'categories' => CategoryHomeResource::collection($categories),
                'pagination' => $this->getPaginationData($categories)]);
        } catch (ModelNotFoundException $e) {
            Log::error('ModelNotFoundException in index: ' . $e->getMessage());
            return $this->notFoundResponse('CATEGORIES_NOT_FOUND');
        } catch (Exception $e) {
            Log::error('Exception in index: ' . $e->getMessage());
            return $this->genericErrorResponse();
        }
    }



    public function show( $id, $subCategoryId = null)
    {


        try {
            $category = $this->getCategoryById($id);
            $products = $this->getCategoryProducts($category, $subCategoryId);

            if ($subCategoryId && !$this->subcategoryExists($category, $subCategoryId)) {
                return $this->notFoundResponse('SUBCATEGORY_NOT_FOUND');
            }
            if ($subCategoryId) {
                $category = Category::findOrFail($id);
                return $this->successResponse([
                    'category' => new CategoryDetailResource($category),
                    'products' => ProductResource::collection($products),
                    'pagination' => $this->getPaginationData($products)
                ]);
            }

            return $this->successResponse([
                'category' => new CategoryDetailResource($category),
                'products' => ProductResource::collection($products),
                'pagination' => $this->getPaginationData($products)
            ]);
        } catch (ModelNotFoundException $e) {
            Log::error('ModelNotFoundException in show: ' . $e->getMessage());
            return $this->notFoundResponse('CATEGORY_NOT_FOUND');
        } catch (Exception $e) {
            Log::error('Exception in show: ' . $e->getMessage());
            return $this->genericErrorResponse();
        }
    }
}
