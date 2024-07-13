<?php

namespace App\Http\Controllers\Api\Categories;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class CategoryController extends Controller
{
    public function index()
    {
        try {
            $categories = Category::paginate(6);
            $categoryData = CategoryResource::collection($categories);
            return response()->json([
                'code' => 'SUCCESS',
                'data' => $categoryData,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'code' => 'ERROR',
                'data' => (object)['CATEGORIES_NOT_FOUND'],
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'code' => 'ERROR',
                'data' => (object)['GENERIC_ERROR'],
            ], 500);
        }
    }
}
