<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PurchaseController;

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

Route::get('/', [ItemController::class, 'index'])->name('items.index');
Route::get('/item/{item}', [ItemController::class, 'detail'])->name('items.detail');

Route::middleware('auth')->group(function () {
    Route::get('/', [ItemController::class, 'search'])->name('items.search');
    Route::get('/sell',  [ItemController::class, 'sellView'])->name('items.sell.view');
    Route::post('/sell', [ItemController::class, 'sellCreate'])->name('items.sell.create');

    Route::post('/item/like/{item_id}',   [LikeController::class, 'create'])->name('likes.create');
    Route::post('/item/unlike/{item_id}', [LikeController::class, 'delete'])->name('likes.delete');

    Route::post('/item/comments/{item_id}', [CommentController::class, 'create'])->name('comments.create');

    Route::get('/purchase/{item_id}',         [PurchaseController::class, 'index'])->middleware('purchase')->name('purchase.index');
    Route::post('/purchase/{item_id}',        [PurchaseController::class, 'purchase'])->middleware('purchase')->name('purchase.execute');
    Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'address'])->middleware('purchase')->name('purchase.address');
    Route::post('/purchase/address/{item_id}',[PurchaseController::class, 'updateAddress'])->middleware('purchase')->name('purchase.address.update');

    Route::get('/mypage',          [UserController::class, 'mypage'])->name('mypage');
    Route::get('/mypage/profile',  [UserController::class, 'profile'])->name('mypage.profile');
    Route::post('/mypage/profile', [UserController::class, 'updateProfile'])->name('profile.update');
});
