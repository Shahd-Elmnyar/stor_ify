<?php

namespace App\Http\Controllers\Api\Categories;

use Exception;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CategoryDetailResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $user = $this->getUser($request);
        if (!$user) {
            return $this->unauthorizedResponse();
        }

        try {
            $categories = $this->getCategories(8);
            return $this->successResponse(CategoryResource::collection($categories));
        } catch (ModelNotFoundException $e) {
            Log::error('ModelNotFoundException in index: ' . $e->getMessage());
            return $this->notFoundResponse('CATEGORIES_NOT_FOUND');
        } catch (Exception $e) {
            Log::error('Exception in index: ' . $e->getMessage());
            return $this->genericErrorResponse();
        }
    }



    public function show(Request $request, $id, $subCategoryId = null)
    {
        $user = $this->getUser($request);
        if (!$user) {
            return $this->unauthorizedResponse();
        }

        try {
            $category = $this->getCategoryById($id);
            $products = $this->getCategoryProducts($category, $subCategoryId);

            if ($subCategoryId && !$this->subcategoryExists($category, $subCategoryId)) {
                return $this->notFoundResponse('SUBCATEGORY_NOT_FOUND');
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
