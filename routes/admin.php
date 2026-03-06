<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BannerItemController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ChatsController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\ContactUsController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\DriverController;
use App\Http\Controllers\Admin\DriverMapController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\ErrorController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\LogisticServiceController;
use App\Http\Controllers\Admin\ManagerController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\PermissionsController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\RegionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SocialMediaController;
use App\Http\Controllers\Admin\StaticPageController;
use App\Http\Controllers\Admin\SubscribeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VisitorController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Routes for the admin panel, including authentication and resource management.
|
*/


// Authentication Routes
Route::prefix('admin')->name('admin.')->middleware('guest:admin')->group(function () {
    Route::get('login', [AdminAuthController::class, 'loginPage'])->name('login.page');
    Route::post('login/post', [AdminAuthController::class, 'login'])->name('login');

    // Password Reset Routes
    Route::get('forgot-password', [AdminAuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('forgot-password', [AdminAuthController::class, 'sendResetOtp'])->name('password.email');
    Route::get('reset-password/{token}', [AdminAuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('reset-password', [AdminAuthController::class, 'resetPassword'])->name('password.update');
});

// Admin Routes (Authenticated)
Route::prefix('admin')->as('admin.')->middleware('auth:admin')->group(function () {
    // Dashboard
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');
    Route::get('/visitors/chart', [VisitorController::class, 'chartData'])
        ->name('visitors.chart');

    // Settings
    Route::prefix('settings')->as('setting.')->group(function () {
        Route::get('pages', [SettingController::class, 'pages'])->name('pages');
        Route::get('edit', [SettingController::class, 'edit'])->name('edit');
        Route::post('update', [SettingController::class, 'update'])->name('update');
        Route::post('update-pages', [SettingController::class, 'updatepages'])->name('updatepages');
    });

    // Resource Routes
    Route::resources([
        'admins' => AdminController::class,
        // 'permissions' => PermissionsController::class,
        'roles' => RolesController::class,
        'countries' => CountryController::class,
        'contactus' => ContactUsController::class,
        'faqs' => FaqController::class,
        'logistic-services' => LogisticServiceController::class,
        'employees' => EmployeeController::class,
        'managers' => ManagerController::class,
        'regions' => RegionController::class,
    ]);

    // coupons
    Route::prefix('coupons')->name('coupons.')->group(function () {
        //  Route::resource('/', CouponController::class);
        Route::get('/', [CouponController::class, 'index'])->name('index');
        Route::get('/create', [CouponController::class, 'create'])->name('create');
        Route::post('/', [CouponController::class, 'store'])->name('store');
        Route::get('/{coupon}/edit', [CouponController::class, 'edit'])->name('edit');
        Route::put('/{coupon}', [CouponController::class, 'update'])->name('update');
        Route::delete('/{coupon}', [CouponController::class, 'destroy'])->name('destroy');
        Route::get('/{coupon}', [CouponController::class, 'show'])->name('show');
        Route::post('bulk-action', [CouponController::class, 'bulkAction'])->name('bulk-action');
        Route::post('{coupon}/duplicate', [CouponController::class, 'duplicate'])->name('duplicate');
        Route::post('generate-code', [CouponController::class, 'generateCode'])->name('generate-code');
        Route::post('validate-code', [CouponController::class, 'validateCode'])->name('validate-code');
        Route::get('export', [CouponController::class, 'export'])->name('export');
    });

    // errors
    Route::prefix('errors')->name('errors.')->group(function () {
        Route::get('/', [ErrorController::class, 'index'])->name('index');
        Route::get('/php-errors', [ErrorController::class, 'phpErrors'])->name('php-errors');
        Route::get('/search', [ErrorController::class, 'search'])->name('search');
        Route::get('/download/{filename}', [ErrorController::class, 'download'])->name('download');
        Route::delete('/destroy', [ErrorController::class, 'destroy'])->name('destroy');
        Route::post('/clear-all', [ErrorController::class, 'clearAll'])->name('clear-all');
    });

    // social-media
    Route::prefix('social-media')->name('social-media.')->group(function () {
        Route::get('/', [SocialMediaController::class, 'index'])->name('index');
        Route::get('/{id}/edit', [SocialMediaController::class, 'edit'])->name('edit');
        Route::put('/{id}', [SocialMediaController::class, 'update'])->name('update');
        Route::post('/bulk-update', [SocialMediaController::class, 'bulkUpdate'])->name('bulk-update');
    });

    // Contacts
    Route::prefix('contacts')->as('contact.')->group(function () {
        Route::get('/', [ContactController::class, 'index'])->name('index');
        Route::get('read/{id}', [ContactController::class, 'read'])->name('read');
        Route::delete('delete/{id}', [ContactController::class, 'destroy'])->name('destroy');
    });

    // Subscriptions
    Route::prefix('subscriptions')->as('subscribe.')->group(function () {
        Route::get('/', [SubscribeController::class, 'index'])->name('index');
    });



    // Payment Method
    Route::resource('payment-methods', PaymentMethodController::class);
    Route::patch('payment-methods/{paymentMethod}/toggle-status', [PaymentMethodController::class, 'toggleStatus'])->name('payment-methods.toggle-status');

    // Users

    // Users Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');

        // User Status
        Route::post('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');

        // User Wallet
        Route::get('/{user}/wallet', [UserController::class, 'wallet'])->name('wallet');

        // User Notifications
        Route::post('/{user}/send-notification', [UserController::class, 'sendNotification'])->name('send-notification');

        // User Orders
        Route::get('/{user}/orders', [UserController::class, 'orders'])->name('orders');

        // User Contracts
        Route::get('/{user}/contracts', [UserController::class, 'contracts'])->name('contracts');

        // User Device Tokens
        Route::get('/{user}/devices', [UserController::class, 'devices'])->name('devices');

        // Export Users
        Route::get('/export/excel', [UserController::class, 'exportExcel'])->name('export.excel');
        Route::get('/export/pdf', [UserController::class, 'exportPdf'])->name('export.pdf');

        // Bulk Actions
        Route::post('/bulk/toggle-status', [UserController::class, 'bulkToggleStatus'])->name('bulk.toggle-status');
        Route::post('/bulk/delete', [UserController::class, 'bulkDelete'])->name('bulk.delete');
    });
    // Banner Routes
    Route::prefix('banners')->name('banners.')->group(function () {
        Route::get('/', [BannerController::class, 'index'])->name('index');
        Route::get('/create', [BannerController::class, 'create'])->name('create');
        Route::post('/', [BannerController::class, 'store'])->name('store');
        Route::get('/{banner}', [BannerController::class, 'show'])->name('show');
        Route::get('/{banner}/edit', [BannerController::class, 'edit'])->name('edit');
        Route::put('/{banner}', [BannerController::class, 'update'])->name('update');
        Route::delete('/{banner}', [BannerController::class, 'destroy'])->name('destroy');
        Route::post('/{banner}/toggle-status', [BannerController::class, 'toggleStatus'])->name('toggle-status');

        // Banner Items Routes - إضافة route للعرض
        Route::get('/items/{bannerItem}', [BannerItemController::class, 'show'])->name('items.show'); // أضف هذا السطر
        Route::post('/items', [BannerItemController::class, 'store'])->name('items.store');
        Route::put('/items/{bannerItem}', [BannerItemController::class, 'update'])->name('items.update');
        Route::delete('/items/{bannerItem}', [BannerItemController::class, 'destroy'])->name('items.destroy');
        Route::post('/items/{bannerItem}/toggle-status', [BannerItemController::class, 'toggleStatus'])->name('items.toggle-status');
        Route::post('/items/reorder', [BannerItemController::class, 'reorder'])->name('items.reorder');
    });

    // Orders
    Route::prefix('orders')->as('orders.')->group(function () {
        // Route::resource('/', OrderController::class);
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/create', [OrderController::class, 'create'])->name('create');
        Route::post('/', [OrderController::class, 'store'])->name('store');
        Route::get('/{id}', [OrderController::class, 'show'])->name('show');
        Route::get('/{order}/edit', [OrderController::class, 'edit'])->name('edit');
        Route::put('/{order}', [OrderController::class, 'update'])->name('update');
        Route::delete('/{order}', [OrderController::class, 'destroy'])->name('destroy');
        Route::get('/{order}/tracking', [OrderController::class, 'tracking'])->name('tracking');
        Route::get('update-payment-status/{order}', [OrderController::class, 'updatePaymentStatus'])->name('update-payment-status');
        Route::get('assign-driver/{order}', [OrderController::class, 'assignDriver'])->name('assign-driver');
        Route::get('accept-offer/{order}', [OrderController::class, 'acceptOffer'])->name('accept-offer');
        Route::get('cancel/{order}', [OrderController::class, 'cancelOrder'])->name('cancel');
        Route::post('/{order}/update-status', [OrderController::class, 'updateStatus'])->name('update-status');

        Route::get('/{order}/print', [OrderController::class, 'print'])->name('print');
        Route::get('/export', [OrderController::class, 'export'])->name('export');
    });

    // Routes for Roles and Permissions
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::get('/create', [RoleController::class, 'create'])->name('create');
        Route::post('/', [RoleController::class, 'store'])->name('store');
        Route::get('/{role}', [RoleController::class, 'show'])->name('show');
        Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit');
        Route::put('/{role}', [RoleController::class, 'update'])->name('update');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
        Route::get('/{role}/permissions', [RoleController::class, 'permissions'])->name('permissions');
        Route::post('/{role}/permissions', [RoleController::class, 'syncPermissions'])->name('permissions.sync');
    });

    Route::prefix('permissions')->name('permissions.')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])->name('index');
        Route::get('/create', [PermissionController::class, 'create'])->name('create');
        Route::post('/', [PermissionController::class, 'store'])->name('store');
        Route::post('/generate', [PermissionController::class, 'generateForModule'])->name('generate');
        Route::get('/{permission}/edit', [PermissionController::class, 'edit'])->name('edit');
        Route::put('/{permission}', [PermissionController::class, 'update'])->name('update');
        Route::delete('/{permission}', [PermissionController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('articles')->name('articles.')->group(function () {
        // مقالات
        Route::resource('/', ArticleController::class);
        Route::post('/bulk-actions', [ArticleController::class, 'bulkActions'])->name('bulk-actions');
        Route::patch('/{article}/toggle-status', [ArticleController::class, 'toggleStatus'])->name('toggle-status');
        Route::patch('/{article}/toggle-featured', [ArticleController::class, 'toggleFeatured'])->name('toggle-featured');
    });

    // إحصائيات المقالات
    Route::get('/articles/statistics', [ArticleController::class, 'statistics'])->name('articles.statistics');

    Route::prefix('assign-roles')->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'assignIndex'])->name('assign.index');
        Route::post('/', [RoleController::class, 'assignRoles'])->name('assign.store');
    });
    Route::prefix('static-pages')->name('static-pages.')->group(function () {
        Route::resource('/', StaticPageController::class);
        Route::post('/bulk-action', [StaticPageController::class, 'bulkAction'])
            ->name('bulk-action');
    });

    // المحادثات
    Route::prefix('chats')->name('chats.')->group(function () {
        Route::get('/', [ChatsController::class, 'index'])->name('index');
        Route::get('/live', [ChatsController::class, 'live'])->name('live');
        Route::get('/statistics', [ChatsController::class, 'statistics'])->name('statistics');
        Route::get('/{chat}', [ChatsController::class, 'show'])->name('show');
        Route::delete('/{chat}', [ChatsController::class, 'destroy'])->name('destroy');
        Route::delete('/message/{message}', [ChatsController::class, 'destroyMessage'])->name('destroy-message');
        Route::post('/{chat}/mark-read', [ChatsController::class, 'markRead'])->name('mark-read');
        Route::get('/export', [ChatsController::class, 'export'])->name('export');
        Route::post('/{chat}/send', [ChatsController::class, 'sendMessage'])->name('send-message');
    });

    Route::prefix('admin-chats')->name('adminChats.')->group(function () {
        // محادثاتي المباشرة (Admin Chats)
        Route::get('/', [ChatsController::class, 'adminChats'])->name('index');
        // إنشاء محادثات جديدة
        Route::get('/create', [ChatsController::class, 'create'])->name('create');
        Route::post('/', [ChatsController::class, 'store'])->name('store');

        // البحث والمساعدة
        Route::get('/search-participants', [ChatsController::class, 'searchParticipants'])->name('search-participants');
        Route::get('/get-participant-info', [ChatsController::class, 'getParticipantInfo'])->name('get-participant-info');

        // إدارة المحادثات الفردية
        Route::get('/{chat}', [ChatsController::class, 'show'])->name('show');
        Route::delete('/{chat}', [ChatsController::class, 'destroy'])->name('destroy');
    });

    // Drivers Management
    Route::prefix('drivers')->name('drivers.')->group(function () {
        Route::post('/{id}/delete-image', [DriverController::class, 'deleteImage'])->name('delete-image');
        Route::post('/{id}/reset-verification', [DriverController::class, 'resetVerification'])->name('reset-verification');
        // Resource routes
        Route::get('/', [DriverController::class, 'index'])->name('index');
        Route::get('/create', [DriverController::class, 'create'])->name('create');
        Route::post('/', [DriverController::class, 'store'])->name('store');
        Route::get('/{id}', [DriverController::class, 'show'])->name('show');
        Route::get('/{id}/details', [DriverController::class, 'details'])->name('details');
        Route::get('/{id}/edit', [DriverController::class, 'edit'])->name('edit');
        Route::put('/{id}', [DriverController::class, 'update'])->name('update');
        Route::delete('/{id}', [DriverController::class, 'destroy'])->name('destroy');

        // Custom routes (must come before resource routes with parameters)
        Route::get('/stats/quick', [DriverController::class, 'quickStats'])->name('quick-stats');
        Route::get('/stats/storage-usage', [DriverController::class, 'storageUsage'])->name('storage-usage');
        Route::get('/stats/recent-activities', [DriverController::class, 'recentActivities'])->name('recent-activities');
        Route::get('/stats/system-status', [DriverController::class, 'systemStatus'])->name('system-status');

        Route::post('/clear-cache', [DriverController::class, 'clearCache'])->name('clear-cache');
        Route::post('/toggle-maintenance', [DriverController::class, 'toggleMaintenance'])->name('toggle-maintenance');
        Route::get('/export', [DriverController::class, 'export'])->name('export');

        // Driver specific actions
        Route::prefix('{id}')->group(function () {
            Route::post('/approve', [DriverController::class, 'approve'])->name('approve');
            Route::post('/reject', [DriverController::class, 'reject'])->name('reject');
            Route::post('/toggle-status', [DriverController::class, 'toggleStatus'])->name('toggle-status');
            Route::get('/wallet', [DriverController::class, 'wallet'])->name('wallet');
        });
    });

    // Services Management
    Route::prefix('services')->name('services.')->group(function () {
        Route::get('/', [ServiceController::class, 'index'])->name('index');
        Route::post('/', [ServiceController::class, 'store'])->name('store');
        Route::put('{service}', [ServiceController::class, 'update'])->name('update');
        Route::delete('{service}', [ServiceController::class, 'destroy'])->name('destroy');
        Route::post('{service}/toggle-status', [ServiceController::class, 'toggleStatus'])->name('toggle-status');
    });

    // Driver Map
    Route::prefix('driver-map')->name('drivers.map.')->group(function () {
        Route::get('/', [DriverMapController::class, 'index'])->name('index');
        Route::get('/locations', [DriverMapController::class, 'getLocations'])->name('locations');
        Route::get('/driver/{id}', [DriverMapController::class, 'getDriverDetails'])->name('details');
        Route::get('/search', [DriverMapController::class, 'search'])->name('search');
        Route::get('/filter', [DriverMapController::class, 'filter'])->name('filter');
    });
    Route::get('order/statistics', [OrderController::class, 'statistics'])->name('orders.statistics');
});

// Visitor stats route (outside admin group)
Route::get('/orders/stats/{year}', [VisitorController::class, 'ordersStats']);
