<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Admin\MerchantSwitchToCustomer;
use Illuminate\Support\Facades\Route;

// Installer routes
include 'admin/Installer.php';

include 'admin/Auth.php';

// Admin Routes
Route::middleware('auth')->name('admin.')->prefix('admin')->group(function () {
    include 'admin/Package.php';
    include 'admin/Promos.php';

    //flash deals
    include 'admin/FlashDeal.php';

    // Markerplace Admin only routes
    Route::middleware(['userType:admin'])->group(function () {
        include 'incevio.php';

        Route::namespace('Report')->group(function () {
            include 'admin/Report.php';
            include 'admin/Visitor.php';
        });
    });

    // Merchant only routes
    Route::middleware(['userType:merchant'])->group(function () {
        Route::namespace('Report')->group(function () {
            include 'admin/ShopReport.php';
        });

        Route::get('switchToCustomer', [
            MerchantSwitchToCustomer::class,
            'switchToCustomer'
        ])->name('merchant.switchToCustomer');

        Route::get('createCustomer', [
            MerchantSwitchToCustomer::class,
            'createCustomer'
        ])->name('merchant.createCustomer');
    });

    // Account Routes for Merchant and Admin
    Route::name('account.')->prefix('account')->group(function () {
        include 'admin/Account.php';
        include 'admin/Billing.php';
    });

    Route::get('secretLogout', [
        Admin\DashboardController::class,
        'secretLogout'
    ])->name('secretLogout');

    Route::middleware(['subscribed', 'checkBillingInfo'])->group(function () {
        // Dashboard
        Route::put('dashboard/config/{node}/toggle', [
            Admin\DashboardController::class,
            'toggleConfig'
        ])->name('dashboard.config.toggle')->middleware('ajax');

        Route::get('dashboard', [
            Admin\DashboardController::class,
            'index'
        ])->name('admin.dashboard')->middleware('dashboard');

        Route::get('secretLogin/{user}', [
            Admin\DashboardController::class,
            'secretLogin'
        ])->name('user.secretLogin');

        include 'admin/Notification.php';

        // Merchant Routes for Admin
        Route::name('admin.')->prefix('admin')->group(function () {
            include 'admin/User.php';
            include 'admin/Customer.php';
            include 'admin/DeliveryBoy.php';
        });

        // Vendors Routes for Admin
        Route::name('vendor.')->prefix('seller')->group(function () {
            include 'admin/Merchant.php';
            include 'admin/Shop.php';
        });

        // Catalog Routes for Admin
        Route::name('catalog.')->prefix('catalog')->group(function () {
            include 'admin/CategoryGroup.php';
            include 'admin/CategorySubGroup.php';
            include 'admin/Category.php';
            include 'admin/Product.php';
            include 'admin/Attribute.php';
            include 'admin/AttributeValues.php';
            include 'admin/Manufacturer.php';
        });

        // Stock Routes for Admin
        Route::name('stock.')->prefix('stock')->group(function () {
            include 'admin/Inventory.php';
            include 'admin/Warehouse.php';
            include 'admin/InventoryProduct.php';
            include 'admin/Supplier.php';
        });

        // Shipping Routes for Admin/Merchant
        Route::name('shipping.')->prefix('shipping')->group(function () {
            include 'admin/ShippingZone.php';
            include 'admin/ShippingRate.php';
            include 'admin/Carrier.php';
            // include 'admin/ShippingLabel.php';
        });

        // Order Routes for Admin/Merchant
        Route::name('order.')->prefix('order')->group(function () {
            include 'admin/Order.php';
            include 'admin/Cart.php';
        });

        // Utility Routes for Admin/Merchant
        Route::name('utility.')->prefix('utility')->group(function () {
            include 'admin/EmailTemplate.php';
            include 'admin/PdfTemplate.php';
            include 'admin/Faq.php';
            include 'admin/Page.php';
            include 'admin/Blog.php';
        });

        // Settings Routes for Admin/Merchant
        Route::name('setting.')->prefix('setting')->group(function () {
            include 'admin/UserRole.php';
            include 'admin/Tax.php';
            include 'admin/Config.php';
            // include('admin/Package.php');
            include 'admin/System.php';
            include 'admin/SystemConfig.php';
            include 'admin/PaymentConfig.php';
            include 'admin/ShippingConfig.php';
            include 'admin/SubscriptionPlan.php';
            include 'admin/Country.php';
            include 'admin/State.php';
            include 'admin/Currency.php';
            include 'admin/Language.php';
            include 'admin/Verification.php';
        });

        // Appearances Routes for Admin
        Route::name('appearance.')->prefix('appearance')->group(function () {
            include 'admin/Theme.php';
            include 'admin/Banner.php';
            include 'admin/Slider.php';
            include 'admin/CustomCSS.php';
            // include 'admin/CustomInvoice.php';
        });

        // Promotions Routes for Admin
        Route::name('promotion.')->prefix('promotion')->group(function () {
            include 'admin/Coupon.php';
            include 'admin/GiftCard.php';
        });

        // Support Routes for Admin
        Route::name('support.')->prefix('support')->group(function () {
            include 'admin/Chat.php';
            include 'admin/Message.php';
            include 'admin/Ticket.php';
            include 'admin/Dispute.php';
            include 'admin/Refund.php';
        });

        // Others
        // Route::resource('role', 'RoleController');
        // Route::resource('comment', 'CommentController');

        // AJAX routes
        Route::middleware('ajax')->group(function () {
            Route::get('catalog/ajax/getParentAttributeType', [
                Admin\AttributeController::class,
                'ajaxGetParentAttributeType'
            ])->name('ajax.getParentAttributeType');

            Route::get('order/ajax/filterShippingOptions', [
                Admin\AjaxController::class,
                'filterShippingOptions'
            ])->name('ajax.filterShippingOptions');
        });
    });
});
