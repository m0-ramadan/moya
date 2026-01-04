<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\SliderController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\ContractController;
use App\Http\Controllers\Api\ContactUsController;
use App\Http\Controllers\Api\StaticPagesController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\Website\HomeController;
use App\Http\Controllers\Api\Website\PageController;
use App\Http\Controllers\Api\SavedLocationController;
use App\Http\Controllers\Api\ArticleCommentController;
use App\Http\Controllers\Api\ArticleCategoryController;
use App\Http\Controllers\Api\ArticleInteractionController;
use App\Http\Controllers\Api\Website\UserAddressController;


Route::prefix('v1')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('addresses')->group(function () {
            Route::get('/', [UserAddressController::class, 'index']);
            Route::get('/{id}', [UserAddressController::class, 'show']);
            Route::post('/', [UserAddressController::class, 'store']);
            Route::put('/{id}', [UserAddressController::class, 'update']);
            Route::delete('/{id}', [UserAddressController::class, 'destroy']);
        });

        Route::prefix('auth')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/user', [AuthController::class, 'user']);
            Route::post('/complete-profile', [AuthController::class, 'completeProfile']);
        });

        // إنشاء طلب
        Route::prefix('orders')->group(function () {
            Route::post('/', [OrderController::class, 'store']);
            Route::get('/', [OrderController::class, 'index']);
            Route::get('/statuses', [OrderController::class, 'statuses']);
            Route::get('/{id}', [OrderController::class, 'show']);
        });

        Route::patch('/user/settings/notifications', [AuthController::class, 'updateNotifications']);

        // عقود المستخدم
        Route::prefix('contracts')->group(function () {
            Route::get('/', [ContractController::class, 'index']);
            Route::post('/', [ContractController::class, 'store']);
            Route::get('/active', [ContractController::class, 'active']);
            Route::get('/{id}', [ContractController::class, 'show']);
            Route::post('/{id}/renew', [ContractController::class, 'renew']);
            Route::post('/{id}/cancel', [ContractController::class, 'cancel']);

            // مدفوعات العقد
            Route::post('/{contractId}/payments', [ContractController::class, 'addPayment']);
            // مواقع توصيل العقد
            Route::post('/{contractId}/locations', [ContractController::class, 'addDeliveryLocation']);
            Route::delete('/{contractId}/locations/{locationId}', [ContractController::class, 'removeDeliveryLocation']);
        });

        // Notification routes
        Route::prefix('notifications')->group(function () {
            Route::get('/', [NotificationController::class, 'index']);
            Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
            Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
            Route::delete('/clear-all', [NotificationController::class, 'clearAll']);
            Route::post('/{notification}/mark-read', [NotificationController::class, 'markAsRead']);
            Route::delete('/{notification}', [NotificationController::class, 'destroy']);
            // الطرق الجديدة لإرسال الإشعارات
            Route::post('/send-to-user', [NotificationController::class, 'sendToUser']);
            Route::post('/send-to-multiple', [NotificationController::class, 'sendToMultipleUsers']);
            Route::post('/send-test/{userId}', [NotificationController::class, 'sendTestToUser']);

            Route::get('/user-devices/{userId}', [NotificationController::class, 'getUserDevices']);

            // Admin only route
            Route::post('/test-firebase', [NotificationController::class, 'testFirebase']);
        });
    });
    Route::get('/services', [ServiceController::class, 'index']);
    Route::get('/type-water', [ServiceController::class, 'typeWater']);

    Route::get('/sliders', [SliderController::class, 'index']);

    Route::get('/banners', [SliderController::class, 'banners']);

    Route::prefix('auth')->group(function () {
        Route::post('/send-otp', [AuthController::class, 'sendOtp']);
        Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
        Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
        Route::post('/complete-profile', [AuthController::class, 'completeProfile']);
    });

    Route::get('home', [HomeController::class, 'index']);
    Route::get('static-pages/{slug}', [StaticPagesController::class, 'index']);


    // المجموعة الرئيسية للمقالات
    Route::prefix('articles')->group(function () {
        // عرض المقالات
        Route::get('/', [ArticleController::class, 'index']);
        Route::get('/featured', [ArticleController::class, 'featured']);
        Route::get('/latest', [ArticleController::class, 'latest']);
        Route::get('/random', [ArticleController::class, 'random']);
        Route::get('/search', [ArticleController::class, 'search']);

        // مقالة محددة
        Route::get('/{slug}', [ArticleController::class, 'show']);

        // تفاعلات المقالة (تتطلب مصادقة)
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/{id}/like', [ArticleInteractionController::class, 'like']);
            Route::post('/{id}/bookmark', [ArticleInteractionController::class, 'bookmark']);
            Route::post('/{id}/share', [ArticleInteractionController::class, 'share']);
        });
    });
    // تعليقات المقالات
    Route::prefix('articles/{articleId}/comments')->group(function () {
        Route::get('/', [ArticleCommentController::class, 'index']);
        Route::post('/', [ArticleCommentController::class, 'store']);

        // إعجاب بالتعليق (تتطلب مصادقة)
        //  Route::middleware('auth:sanctum')->post('/{commentId}/like', [ArticleCommentController::class, 'like']);
    });

    // أقسام المقالات
    Route::prefix('categories')->group(function () {
        Route::get('/', [ArticleCategoryController::class, 'index']);
        Route::get('/{slug}', [ArticleCategoryController::class, 'show']);
        Route::get('/{slug}/articles', [ArticleCategoryController::class, 'articles']);
    });


    // التعليقات العامة
    Route::prefix('comments')->middleware('auth:sanctum')->group(function () {
        Route::post('/{commentId}/like', [ArticleCommentController::class, 'like']);
    });


    Route::post('contact-us', [ContactUsController::class, 'store']);


    // حفظ عنوان
    Route::post('/locations', [SavedLocationController::class, 'store']);
    Route::get('pages/{key}', [PageController::class, 'show']);

    // Notification routes
    Route::prefix('notifications')->group(function () {
        // إدارة أجهزة المستخدم
        Route::post('/register-device', [NotificationController::class, 'registerDeviceToken']);
        Route::post('/remove-device', [NotificationController::class, 'removeDeviceToken']);
    });
});
