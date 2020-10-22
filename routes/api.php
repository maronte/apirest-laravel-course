<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/**
 * Buyers
 */
Route::Resource('buyers', 'Buyer\BuyerController', ['only' => ['index','show']]);

/**
 * Sellers
 */
Route::resource('sellers', 'Seller\SellerController', ['only' => ['index','show']]);

/**
 * Users
 */
Route::apiResource('users', 'User\UserController');

/**
 * Products
 */
Route::resource('products', 'Product\ProductController', ['only' => ['index','show']]);

/**
 * Transactions
 */
Route::resource('transactions', 'Transaction\TransactionController', ['only' => ['index','show']]);

/**
 * Categories
 */
Route::apiResource('categories', 'Category\CategoryController');