<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\ProductsController;

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

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {
    Route::get('/logout', [AdminAuthController::class, 'adminLogout'])->name('adminLogout');
    Route::get('/login', [AdminAuthController::class, 'getLogin'])->name('adminLogin');
    Route::post('/login', [AdminAuthController::class, 'postLogin'])->name('adminLoginPost');

    Route::group(['middleware' => 'adminauth'], function () {
        Route::get('/', function () {
            return view('admin');
        })->name('adminDashboard');
        Route::get('/products/create', [ProductsController::class, 'create'])->name('createProduct');
        Route::post('/products', [ProductsController::class, 'store'])->name('storeProduct');
        Route::get('/products/update/{id}', [ProductsController::class, 'update'])->name('getUpdateProduct');
        Route::post('/products/update/{id}', [ProductsController::class, 'update'])->name('postUpdateProduct');
        Route::get('/products', [ProductsController::class, 'index'])->name('getProducts');

    });
});
