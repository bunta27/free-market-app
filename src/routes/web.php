<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\TradeController;
use App\Http\Controllers\TradeMessageController;
use App\Http\Controllers\TradeReviewController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

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
Route::get('/items/search', [ItemController::class, 'search'])->middleware('auth')->name('items.search');
Route::get('/items/{item}', [ItemController::class, 'detail'])->name('items.detail');

Route::middleware('auth')->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();

        return redirect()->route('mypage.profile', ['verified' => 1]);
    })->middleware('signed')->name('verification.verify');
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    })->middleware('throttle:6,1')->name('verification.send');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/sell',  [ItemController::class, 'sellView'])->name('items.sell.view');
    Route::post('/sell', [ItemController::class, 'sellCreate'])->name('items.sell.create');

    Route::post('/item/like/{item_id}',   [LikeController::class, 'create'])->name('likes.create');
    Route::post('/item/unlike/{item_id}', [LikeController::class, 'delete'])->name('likes.delete');

    Route::post('/item/comments/{item_id}', [CommentController::class, 'create'])->name('comments.create');

    Route::get('/purchase/{item_id}',          [PurchaseController::class, 'index'])->middleware('purchase')->name('purchase.index');
    Route::post('/purchase/{item_id}',         [PurchaseController::class, 'purchase'])->middleware('purchase')->name('purchase.execute');
    Route::get('/purchase/address/{item_id}',  [PurchaseController::class, 'address'])->middleware('purchase')->name('purchase.address');
    Route::post('/purchase/address/{item_id}',  [PurchaseController::class, 'updateAddress'])->middleware('purchase')->name('purchase.address.update');

    Route::get('/purchase/{item_id}/success', [PurchaseController::class, 'success'])->middleware('purchase')->name('purchase.success');
    Route::get('/purchase/{item_id}/cancel',  [PurchaseController::class, 'cancel'])->middleware('purchase')->name('purchase.cancel');

    Route::get('/mypage', [UserController::class, 'mypage'])->name('mypage');
    Route::get('/mypage/profile', [UserController::class, 'profile'])->name('mypage.profile');
    Route::post('/mypage/profile', [UserController::class, 'updateProfile'])->name('profile.update');

    Route::get('/trades', [TradeController::class, 'index'])->name('trades.index');
    Route::get('/trades/{trade}', [TradeController::class, 'show'])->name('trades.show');
    Route::post('/trades/{trade}/complete', [TradeController::class, 'complete'])->name('trades.complete');

    Route::post('/trades/{trade}/messages', [TradeMessageController::class, 'store'])->name('trade.messages.store');
    Route::get('/trades/messages/{message}/edit', [TradeMessageController::class, 'edit'])->name('trade.messages.edit');
    Route::put('/trades/messages/{message}', [TradeMessageController::class, 'update'])->name('trade.messages.update');
    Route::delete('/trades/messages/{message}', [TradeMessageController::class, 'destroy'])->name('trade.messages.destroy');

    Route::post('/trades/{trade}/reviews', [TradeReviewController::class, 'store'])->name('trade.reviews.store');
});

