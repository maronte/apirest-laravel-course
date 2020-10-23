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

Route::resource('buyers.transactions', 'Buyer\BuyerTransactionController', ['only' => ['index']]);

Route::resource('buyers.products', 'Buyer\BuyerProductController', ['only' => ['index']]);

Route::resource('buyers.categories', 'Buyer\BuyerSellerController', ['only' => ['index']]);

Route::resource('buyers.sellers', 'Buyer\BuyerSellerController', ['only' => ['index']]);

/**
 * Sellers
 */
Route::resource('sellers', 'Seller\SellerController', ['only' => ['index','show']]);

Route::resource('sellers.transactions', 'Seller\SellerTransactionController', 
 ['only' => ['index']]);

Route::resource('sellers.buyers', 'Seller\SellerBuyerController', 
 ['only' => ['index']]);

Route::resource('sellers.categories', 'Seller\SellerCategoryController', 
 ['only' => ['index']]);

 Route::resource('sellers.products', 'Seller\SellerProductController', 
 ['except' => ['show']]);



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

Route::resource('transactions.categories', 'Transaction\TransactionCategoryController', ['only' => ['index']]);

Route::resource('transactions.seller', 'Transaction\TransactionSellerController', ['only' => ['index']]);

/**
 * Categories
 */
Route::apiResource('categories', 'Category\CategoryController');

Route::resource('categories.products', 'Category\CategoryProductController', ['only' => ['index']]);

Route::resource('categories.sellers', 'Category\CategorySellerController', ['only' => ['index']]);

Route::resource('categories.transactions', 'Category\CategoryTransactionController', ['only' => ['index']]);

Route::resource('categories.buyers', 'Category\CategoryBuyerController', ['only' => ['index']]);
