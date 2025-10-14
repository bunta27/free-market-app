<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PurchaseController;

use App\Http\middleware\SoldItemMiddleware;

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

Route::get('/', [ItemController::class, 'index']);
Route::get('/items/{item}', [ItemController::class, 'detail'])->name('items.detail');

Route::middleware('auth')->group(function () {
    Route::get('/sell', [ItemController::class, 'sellView']);
    Route::post('/sell', [ItemController::class, 'sellCreate']);
    Route::post('/items/like/{item_id}/', [LikeController::class, 'create']);
    Route::post('/items/unlike/{item_id}/', [LikeController::class, 'delete']);
    Route::post('/items/comments/{item_id}', [CommentController::class, 'create']);
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'index'])->middleware('purchase')->name('purchase.index');
    Route::post('/purchase/{item_id}', [PurchaseController::class, 'purchase'])->middleware('purchase');
    Route::get('purchase/address/{item_id}', [PurchaseController::class, 'address'])->middleware('purchase')->name('purchase.address');
    Route::post('purchase/address/{item_id}', [PurchaseController::class, 'updateAddress']);
    Route::get('/mypage', [UserController::class, 'mypage']);
    Route::get('/mypage/profile', [UserController::class, 'profile']);
    Route::post('/mypage/profile', [UserController::class, 'updateProfile']);
});