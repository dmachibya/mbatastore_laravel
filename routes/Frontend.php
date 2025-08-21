<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Selling\SellingController;
use App\Http\Controllers\Storefront\BlogController;
use App\Http\Controllers\Storefront\HomeController;
use App\Http\Controllers\Storefront\ShopController;
use App\Http\Controllers\Storefront\AccountController;
use App\Http\Controllers\Storefront\NewsletterController;
use App\Http\Controllers\Storefront\ConversationController;

// Route for storefront
Route::middleware(['storefront', 'hasCookie'])->namespace('Storefront')->group(function () {
    // Newsletter
    Route::post('newsletter', [
        NewsletterController::class, 'subscribe'
    ])->name('newsletter.subscribe')->middleware('xssSanitizer');

    // Chat
    include 'storefront/Chat.php';

    // Auth route for customers
    include 'storefront/Auth.php';
    include 'storefront/Cart.php';
    include 'storefront/Order.php';
    include 'storefront/GiftCard.php';

    Route::middleware(['auth:customer'])->group(function () {
        include 'storefront/Account.php';
        include 'storefront/Feedback.php';

        // Conversations
        Route::post('contact/{slug}', [
            ConversationController::class, 'contact'
        ])->name('seller.contact');

        Route::get('message/{message}/archive', [
            ConversationController::class, 'archive'
        ])->name('message.archive');

        Route::get('my/message/{message}', [
            ConversationController::class, 'show'
        ])->name('message.show');

        Route::post('message/{message}', [
            ConversationController::class, 'reply'
        ])->name('message.reply');
    });


    Route::get('/', [
        HomeController::class, 'index'
    ])->name('homepage');

    Route::get('page/{page}', [
        HomeController::class, 'openPage'
    ])->name('page.open');

    Route::get('product/{slug}', [
        HomeController::class, 'product'
    ])->name('show.product');

    Route::get('product/{slug}/quickView', [
        HomeController::class, 'quickViewItem'
    ])->name('quickView.product')->middleware('ajax');

    Route::get('product/{slug}/offers', [
        HomeController::class, 'offers'
    ])->name('show.offers');

    Route::get('categories', [
        HomeController::class, 'categories'
    ])->name('categories');

    Route::get('category/{slug}', [
        HomeController::class, 'browseCategory'
    ])->name('category.browse');

    Route::get('categories/{slug}', [
        HomeController::class, 'browseCategorySubGrp'
    ])->name('categories.browse');

    Route::get('categorygrp/{slug}', [
        HomeController::class, 'browseCategoryGroup'
    ])->name('categoryGrp.browse');

    // Route::get('shop/reviews/{slug}', [
    //     HomeController::class, 'shopReviews'
    // ])->name('reviews.store');

    Route::get('brand/{slug}', [
        HomeController::class, 'brand'
    ])->name('show.brand');

    Route::get('brand/{slug}/products', [
        HomeController::class, 'brandProducts'
    ])->name('brand.products');

    Route::get('brands', [
        HomeController::class, 'all_brands'
    ])->name('brands');

    Route::get('search', [
        HomeController::class, 'search'
    ])->name('inCategoriesSearch')->middleware('xssSanitizer');

    Route::get('blog', [
        BlogController::class, 'index'
    ])->name('blog');

    Route::any('blog/search', [
        BlogController::class, 'search'
    ])->name('blog.search')->middleware('xssSanitizer');

    Route::get('blog/{slug}', [
        BlogController::class, 'show'
    ])->name('blog.show');

    Route::get('blog/author/{author}', [
        BlogController::class, 'author'
    ])->name('blog.author');

    Route::get('blog/tag/{tag}', [
        BlogController::class, 'tag'
    ])->name('blog.tag');

    // Shop routes
    Route::get('shops', [
        ShopController::class, 'index'
    ])->name('shops');

    Route::get('shop/{slug}', [
        ShopController::class, 'show'
    ])->name('show.store');

    Route::get('shop/{slug}/products', [
        ShopController::class, 'products'
    ])->name('shop.products');

    Route::get('shop/{slug}/reviews', [
        ShopController::class, 'reviews'
    ])->name('shop.reviews');
});

Route::get('switchToMerchant', [
    AccountController::class, 'switchToMerchant'
])->name('customer.switchToMerchant');

// Route for merchant landing theme
Route::middleware('selling')
    ->namespace('Selling')->group(function () {
        Route::get('selling', [
            SellingController::class, 'index'
        ])->name('selling');
    });

// Route for customers
// Route::group(['as' => 'customer.', 'prefix' => 'customer'], function() {
	// include('storefront/Auth.php');
// });
