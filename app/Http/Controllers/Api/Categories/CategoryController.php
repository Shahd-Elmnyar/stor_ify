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
            $categories = $this->getCategories();
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

    private function getUser(Request $request)
    {
        return $request->user();
    }

    private function unauthorizedResponse()
    {
        return response()->json([
            'code' => 'ERROR',
            'data' => 'USER_NOT_AUTH',
        ], 401);
    }

    private function notFoundResponse($message)
    {
        return response()->json([
            'code' => 'ERROR',
            'data' => $message,
        ], 404);
    }

    private function genericErrorResponse()
    {
        return response()->json([
            'code' => 'ERROR',
            'data' => 'GENERIC_ERROR',
        ], 500);
    }

    private function successResponse($data)
    {
        return response()->json([
            'code' => 'SUCCESS',
            'data' => $data,
        ]);
    }

    private function getCategories()
    {
        return Category::with('subCategories')->paginate(6);
    }

    private function getCategoryById($id)
    {
        return Category::with('subCategories')->findOrFail($id);
    }

    private function getCategoryProducts(Category $category, $subCategoryId)
    {
        $query = $category->products()->with('images', 'subCategory', 'colors', 'sizes');

        if ($subCategoryId) {
            $query->where('sub_category_id', $subCategoryId);
        }

        return $query->paginate(6);
    }

    private function subcategoryExists(Category $category, $subCategoryId)
    {
        return $category->subCategories()->where('id', $subCategoryId)->exists();
    }

    private function getPaginationData($products)
    {
        return [
            'total' => $products->total(),
            'per_page' => $products->perPage(),
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),
            'next_page_url' => $products->nextPageUrl(),
            'prev_page_url' => $products->previousPageUrl(),
        ];
    }
}
