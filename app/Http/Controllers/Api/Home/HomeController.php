<?php

namespace App\Http\Controllers\Api\Home;

use App\Models\Store;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AppController;
use App\Http\Resources\StoreResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\CategoryResource;

class HomeController extends AppController
{
    public function index(Request $request)
    {

        $topProducts = $this->getTopProducts($this->user->id);

        $categories = $this->getCategory();


        $topStores = $this->getTopStores();


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

    private function getTopProducts()
    {
        $topProducts = Product::with(['store', 'images', 'favorites' => function ($query)  {
            $query->where('user_id', $this->user->id);
        }])
            ->select('products.*', DB::raw('COUNT(order_items.id) as order_count'))
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->groupBy('products.id', 'products.name', 'products.description', 'products.price', 'products.discount', 'products.store_id', 'products.sub_category_id', 'products.created_at', 'products.updated_at', 'products.category_id')
            ->orderByDesc('order_count')
            ->paginate(3);

        if ($topProducts->isEmpty()) {
            $topProducts = Product::with(['store', 'images', 'favorites' => function ($query)  {
                $query->where('user_id', $this->user->id);
            }])
                ->inRandomOrder()
                ->paginate(3);
        }
        return $topProducts;
    }

    private function getTopStores()
    {
        $topStores = Store::with('categories') // Eager load categories
            ->join('category_store', 'stores.id', '=', 'category_store.store_id')
            ->join('products', 'stores.id', '=', 'products.store_id')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->select('stores.*', DB::raw('COUNT(order_items.id) as total_products_ordered'))
            ->groupBy('stores.id', 'stores.name', 'stores.img', 'stores.created_at', 'stores.updated_at')
            ->orderByDesc('total_products_ordered')
            ->limit(2)
            ->get();

        if ($topStores->isEmpty()) {
            $topStores = Store::inRandomOrder()->limit(2)->get();
        }

        return $topStores;
    }

    public function getCategory()
    {
        $categories = Category::limit(2)->get();
        if ($categories->isEmpty()) {
            return $this->notFoundResponse('NO_CATEGORIES');
        }
        return $categories;
    }
}
