<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Cart\CartController;
use App\Http\Controllers\Api\Home\HomeController;
use App\Http\Controllers\Api\Stores\StoreController;
use App\Http\Controllers\Api\Auth\ValidateOtpController;
use App\Http\Controllers\Api\Favorite\ProductController;
use App\Http\Controllers\Api\Checkout\CheckoutController;
use App\Http\Controllers\Api\Auth\ForgetPasswordController;
use App\Http\Controllers\Api\Auth\UpdatePasswordController;
use App\Http\Controllers\Api\Categories\CategoryController;
use App\Http\Controllers\APi\Checkout\StripePaymentController;
use App\Http\Controllers\Api\Favorite\StoreController as FavoriteStoreController;
use App\Http\Controllers\Api\Products\ProductController as ProductsProductController;
use App\Http\Controllers\Api\Settings\langController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('lang')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forget-password', [ForgetPasswordController::class, 'forgetPassword']);
    Route::post('/validate-otp', [ValidateOtpController::class, 'validateOtp']);
    Route::post('/reset-password', [UpdatePasswordController::class, 'updatePassword']);

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::apiResource('/home', HomeController::class);

        Route::apiResource('categories', CategoryController::class);
        Route::get('categories/{categoryId}/{subCategoryId?}', [CategoryController::class, 'show']);

        Route::get('stores/{CategoryId?}', [StoreController::class, 'getStores']);
        Route::get('stores/offer/{storeId}', [StoreController::class, 'getProductsWithDiscount']);
        Route::get('stores/branches/{storeId}', [StoreController::class, 'getBranches']);
        Route::get('stores/category/{storeId}', [StoreController::class, 'getStoreCategories']);

        Route::apiResource('favorites', ProductController::class)->only(['index', 'store', 'destroy']);
        Route::apiResource('favoritesStore', FavoriteStoreController::class)->only(['index', 'store', 'destroy']);

        Route::post('search', [ProductsProductController::class, 'search']);

        Route::post('addProduct/{product_id}', [CartController::class, 'addProductToCart']);
        Route::get('/cart', [CartController::class, 'showCart']);
        Route::delete('cart/{productId}', [CartController::class, 'deleteProductFromCart']);

        Route::post('/checkout', [CheckoutController::class, 'checkout']);
        Route::post('/checkout/{orderId}/payment-method', [CheckoutController::class, 'updatePaymentMethod']);
        Route::post('/pay/{orderId}', [StripePaymentController::class, 'stripePost']);

        Route::post('/change-lang', [langController::class, 'changeLang']);

    });
});
