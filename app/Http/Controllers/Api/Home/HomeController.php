<?php

namespace App\Http\Controllers\Api\Home;

use App\Models\Store;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AppController;
use App\Http\Resources\CategoryHomeResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\StoreHomeResource;

class HomeController extends AppController
{
    protected $store;
    protected $product;
    protected $category;

    public function __construct(Store $store, Product $product, Category $category)
    {
        parent::__construct();
        $this->store    = $store;
        $this->product  = $product;
        $this->category = $category;
    }

    public function index()
    {
        $topProducts = $this->getTopProducts($this->user->id);
        $categories  = $this->getCategory(2);
        $topStores   = $this->getTopStores(2);
        return $this->successResponse([
                'bestSelling' => ProductResource::collection($topProducts),
                'pagination'  => $this->getPaginationData($topProducts),
                'categories'  => CategoryHomeResource::collection($categories),
                'topStores'   => StoreHomeResource::collection($topStores),
        ]);

    }

    public function searchData(){
        $categories = $this->getCategory(6);
        $topStores  = $this->getTopStores(6);
        return $this->successResponse([
            'topCategories' => CategoryHomeResource::collection($categories),
            'topStores'     => StoreHomeResource::collection($topStores),
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
            $topProducts = Product::with(['store', 'images', 'favorites'  => function ($query)  {
                $query->where('user_id', $this->user->id);
            }])
                ->inRandomOrder()
                ->paginate(3);
        }
        return $topProducts;
    }

    private function getTopStores($limit)
    {
        $topStores = $this->store::with('categories')
            ->join('category_store', 'stores.id', '=', 'category_store.store_id')
            ->join('products', 'stores.id', '=', 'products.store_id')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->select('stores.*', DB::raw('COUNT(order_items.id) as total_products_ordered'))
            ->groupBy('stores.id', 'stores.name', 'stores.img', 'stores.created_at', 'stores.updated_at')
            ->orderByDesc('total_products_ordered')
            ->limit($limit)
            ->get();

        if ($topStores->isEmpty()) {
            $topStores = $this->store::inRandomOrder()->limit(2)->get();
        }

        return $topStores;
    }

    private function getCategory($limit)
    {
        $categories = $this->category::limit($limit)->with('subCategories')->get();
        if ($categories->isEmpty()) {
            return $this->notFoundResponse('NO_CATEGORIES');
        }
        return $categories;
    }
}
