<?php

namespace App\Http\Controllers\Api\Home;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\StoreResource;
use App\Models\Category;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $user = $this->getUser($request);
        if (!$user) {
            return $this->unauthorizedResponse();
        }

        $topProducts = $this->getTopProducts($user->id);

        $categories = $this->getCategory();
        if ($categories->isEmpty()) {
            return $this->notFoundResponse('NO_CATEGORIES');
        }

        $topStores = $this->getTopStores();
        if ($topStores->isEmpty()) {
            return $this->notFoundResponse('NO_TOP_STORES');
        }

        return response()->json([
            'code' => 'SUCCESS',
            'data' => [
                'bestSelling' => ProductResource::collection($topProducts),
                'pagination' => $this->getPaginationData($topProducts),
                'categories' => CategoryResource::collection($categories),
                'topStores' => StoreResource::collection($topStores),
            ]
        ]);
    }

    private function getTopProducts($userId)
    {
        $topProducts = Product::with(['images', 'favorites' => function ($query) use ($userId) {
            $query->where('user_id', $userId);
        }])
            ->select('products.*', DB::raw('COUNT(order_items.id) as order_count'))
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->groupBy('products.id', 'products.name', 'products.description', 'products.price', 'products.discount', 'products.store_id', 'products.sub_category_id', 'products.created_at', 'products.updated_at', 'products.category_id')
            ->orderByDesc('order_count')
            ->paginate(3);

        if ($topProducts->isEmpty()) {
            $topProducts = Product::with(['images', 'favorites' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            }])
                ->inRandomOrder()
                ->paginate(3);
        }
        return $topProducts;
    }



    private function getTopStores()
    {
        $topStores = DB::table('stores')
            ->join('products', 'stores.id', '=', 'products.store_id')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->select('stores.*', DB::raw('COUNT(order_items.id) as total_products_ordered'))
            ->groupBy('stores.id', 'stores.name', 'stores.img', 'stores.category_id', 'stores.created_at', 'stores.updated_at')
            ->orderByDesc('total_products_ordered')
            ->limit(2)
            ->get();

        if ($topStores->isEmpty()) {
            $topStores = DB::table('stores')
                ->inRandomOrder()
                ->limit(2)
                ->get();
        }

        return $topStores;
    }
    public function getCategory()
    {
        return Category::limit(2)->get();
    }

}
