<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function getUser(Request $request)
    {
        return $request->user();
    }

    public function unauthorizedResponse()
    {
        return response()->json([
            'code' => 'ERROR',
            'data' => 'USER_NOT_AUTH',
        ], 401);
    }

    public function notFoundResponse($message)
    {
        return response()->json([
            'code' => 'ERROR',
            'data' => $message,
        ], 404);
    }

    public function genericErrorResponse()
    {
        return response()->json([
            'code' => 'ERROR',
            'data' => (object)['GENERIC_ERROR'],
        ], 500);
    }

    public function successResponse($data=" ")
    {
        return response()->json([
            'code' => 'SUCCESS',
            'data' => $data,
        ]);
    }

    public function getCategories($perPage)
    {
        return Category::with('subCategories')->paginate($perPage);
    }
    public function getCategoryById($id)
    {
        return Category::with('subCategories')->findOrFail($id);
    }

    public function getCategoryProducts(Category $category, $subCategoryId)
    {
        $query = $category->products()->with('images', 'subCategory', 'colors', 'sizes','store');

        if ($subCategoryId) {
            $query->where('sub_category_id', $subCategoryId);
        }

        return $query->paginate(6);
    }

    public function subcategoryExists(Category $category, $subCategoryId)
    {
        return $category->subCategories()->where('id', $subCategoryId)->exists();
    }

    public function getPaginationData($products)
    {
        if ($products instanceof LengthAwarePaginator) {
            return [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'next_page_url' => $products->nextPageUrl(),
                'prev_page_url' => $products->previousPageUrl(),
            ];
        }
        return [];
    }
}
