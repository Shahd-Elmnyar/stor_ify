<?php

namespace App\Http\Controllers\Api\Products;

use Exception;
use App\Models\Cart;
use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\AppController;
use App\Http\Resources\ProductResource;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductController extends AppController
{
    public function search(Request $request)
    {
        try {

            $validated = $request->validate([
                'search' => 'required|string|max:255',
            ]);

            $filters = ['search' => $validated['search']];
            $products = Product::filter($filters)
                ->with(['images', 'sizes'])
                ->paginate(6);

            if ($products->isEmpty()) {
                return $this->notFoundResponse('NO_PRODUCTS_FOUND');
            }

            $productResources = $products->isNotEmpty() ? ProductResource::collection($products) : null;

            return $this->successResponse(
                [
                    'products' => $productResources,
                    'pagination' => $this->getPaginationData($products),
                ]
            );
        } catch (\Exception $e) {
            Log::error('Error during search process: ' . $e->getMessage());
            return $this->genericErrorResponse();
        }
    }
}
