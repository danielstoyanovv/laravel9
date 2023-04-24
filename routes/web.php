<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\ProductsController;
use App\Http\Controllers\ContactsController;
use App\Http\Controllers\Shop\ShopController;
use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\CheckoutController;
use App\Http\Controllers\Shop\PaypalController;
use App\Http\Controllers\Admin\OrdersController;
use App\Http\Controllers\Shop\StripeController;
use App\Http\Controllers\Shop\EpayController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [HomeController::class, 'index'])->name('homepage');

Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {
    Route::get('/logout', [AdminAuthController::class, 'adminLogout'])->name('adminLogout');
    Route::get('/login', [AdminAuthController::class, 'getLogin'])->name('adminLogin');
    Route::post('/login', [AdminAuthController::class, 'postLogin'])->name('adminLoginPost');
});

Route::group(['middleware' => 'adminauth'], function () {
    Route::resource('admin/products', ProductsController::class);
    Route::group(['prefix' => 'admin/orders', 'namespace' => 'Admin'], function () {
        Route::get('/', [OrdersController::class, 'index'])->name('ordersList');
        Route::get('/show/{id}', [OrdersController::class, 'show'])->name('showOrder');
    });
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('adminDashboard');
});

Route::group(['prefix' => 'shop', 'namespace' => 'Shop'], function () {
    Route::get('/', [ShopController::class, 'index'])->name('shop');
    Route::post('/', [ShopController::class, 'index'])->name('search');

    Route::group(['prefix' => 'cart', ], function () {
        Route::get('/', [CartController::class, 'index'])->name('cart');
        Route::post('/addToCart', [CartController::class, 'addToCart'])->name('addToCart');
        Route::post('/removeFromCart', [CartController::class, 'removeFromCart'])->name('removeFromCart');
        Route::post('/checkout', [CheckoutController::class, 'index'])->name('checkout');
        });

    Route::group(['prefix' => 'paypal', 'namespace' => 'Shop'], function () {
        Route::get('/errors', [PaypalController::class, 'error'])->name('paypal_error');
        Route::get('/success', [PaypalController::class, 'success'])->name('paypal_success');
        Route::post('/pay', [PaypalController::class, 'pay'])->name('paypal_pay');
    });

    Route::group(['prefix' => 'stripe', 'namespace' => 'Shop'], function () {
        Route::get('/errors', [StripeController::class, 'error'])->name('stripe_error');
        Route::get('/success', [StripeController::class, 'success'])->name('stripe_success');
        Route::post('/pay', [StripeController::class, 'pay'])->name('stripe_pay');
        Route::post('/refund', [StripeController::class, 'refund'])->name('stripe_refund');
    });

    Route::group(['prefix' => 'epay', 'namespace' => 'Shop'], function () {
        Route::get('/errors', [EpayController::class, 'error'])->name('epay_error');
        Route::get('/success', [EpayController::class, 'success'])->name('epay_success');
        Route::post('/pay', [EpayController::class, 'pay'])->name('epay_pay');
    });
});

Route::post('/sendContactEmail', [ContactsController::class, 'sendContactEmail'])->name('sendContactEmail');

Auth::routes();
