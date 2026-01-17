<?php

use Pusher\Pusher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\RatingController;
use App\Http\Controllers\Api\SliderController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\ContractController;
use App\Http\Controllers\Api\ContactUsController;
use App\Http\Controllers\Api\StaticPagesController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\VoiceMessageController;
use App\Http\Controllers\Api\Website\HomeController;
use App\Http\Controllers\Api\Website\PageController;
use App\Http\Controllers\Api\SavedLocationController;
use App\Http\Controllers\Api\ArticleCommentController;
use App\Http\Controllers\Api\ArticleCategoryController;
use App\Http\Controllers\Api\Driver\DriverAuthController;
use App\Http\Controllers\Api\ArticleInteractionController;
use App\Http\Controllers\Api\Driver\DriverOrderController;
use App\Http\Controllers\Api\Website\UserAddressController;
use App\Http\Controllers\Api\Payment\PaymentCallbackController;
use App\Http\Controllers\Api\User\WalletController as UserWalletController;
use App\Http\Controllers\Api\Driver\WalletController as DriverWalletController;

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
            Route::get('/{id}/status', [OrderController::class, 'checkOrderStatus']);
            Route::post('/{orderId}/rate', [RatingController::class, 'rateDriver']);
            Route::get('/{orderId}/offers', [RatingController::class, 'getOrderOffers']);
            Route::get('/{orderId}/tracking', [DriverOrderController::class, 'getLiveTracking']);

            // طرق الدفع الجديدة
            Route::post('/{orderId}/payment/initiate', [PaymentController::class, 'initiatePayment']);
            Route::get('/{orderId}/payment/status', [PaymentController::class, 'checkPaymentStatus']);
            Route::post('/{orderId}/payment/refund', [PaymentController::class, 'refundPayment']);
            Route::get('/payment-methods', [PaymentController::class, 'getPaymentMethods']);

            // تأكيد السائق (بعد الدفع)
            Route::post('/{orderId}/confirm-driver', [OrderController::class, 'confirmDriver']);

            // للسائقين
            Route::middleware(['driver'])->group(function () {
                Route::post('/{orderId}/accept', [OrderController::class, 'acceptOrder']);
                Route::post('/offers/{offerId}/cancel', [OrderController::class, 'cancelOffer']);
                Route::prefix('driver')->group(function () {
                    Route::post('/{orderId}/update-status', [DriverOrderController::class, 'updateStatus']);
                    Route::post('/{orderId}/update-location', [DriverOrderController::class, 'updateLocation']);
                    Route::get('/{orderId}/path', [DriverOrderController::class, 'getDriverPath']);
                    Route::post('/{orderId}/rate-user', [RatingController::class, 'rateUser']);
                });
            });

            // للمستخدمين
            Route::post('/{orderId}/confirm-driver', [OrderController::class, 'confirmDriver']);
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

        // Chat routes
        Route::prefix('chats')->group(function () {
            Route::get('/', [ChatController::class, 'index']);
            Route::post('/create', [ChatController::class, 'getOrCreateChat']);
            Route::get('/{chat}/messages', [ChatController::class, 'getMessages']);
            Route::post('/{chat}/send', [ChatController::class, 'sendMessage']);
            Route::post('/messages/{message}/read', [ChatController::class, 'markAsRead']);
            Route::delete('/messages/{message}', [ChatController::class, 'deleteMessage']);
            // Chunk upload routes
            Route::post('/{chat}/upload-chunk', [ChatController::class, 'uploadChunk']);
            Route::get('/upload-status', [ChatController::class, 'checkUploadStatus']);
            // Voice message routes
            Route::prefix('voice-messages')->group(function () {
                Route::post('/{chat}/upload', [VoiceMessageController::class, 'uploadVoiceMessage']);
                Route::post('/{chat}/upload-chunked', [VoiceMessageController::class, 'uploadChunkedVoiceMessage']);
                Route::delete('/{message}', [VoiceMessageController::class, 'deleteVoiceMessage']);
            });
        });

        // Pusher authentication
        Route::post('/broadcasting/auth', function (Request $request) {
            $pusher = new Pusher(
                config('broadcasting.connections.pusher.key'),
                config('broadcasting.connections.pusher.secret'),
                config('broadcasting.connections.pusher.app_id'),
                config('broadcasting.connections.pusher.options')
            );

            return $pusher->socket_auth($request->channel_name, $request->socket_id);
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

    // User wallet routes
    Route::middleware(['auth:sanctum'])->prefix('user/wallet')->group(function () {
        Route::get('/', [UserWalletController::class, 'getBalance']);
        Route::get('/transactions', [UserWalletController::class, 'getTransactions']);
        Route::post('/deposit', [UserWalletController::class, 'initiateDeposit']);
        Route::post('/withdraw', [UserWalletController::class, 'withdraw']);
        Route::post('/transfer', [UserWalletController::class, 'transfer']);
    });


    // Driver wallet routes
    Route::middleware(['auth:sanctum', 'driver.only'])->prefix('driver/wallet')->group(function () {
        Route::get('/', [DriverWalletController::class, 'getBalance']);
        Route::get('/earnings', [DriverWalletController::class, 'getEarnings']);
        Route::post('/cashout', [DriverWalletController::class, 'cashOut']);
    });

    // Payment callback
    Route::middleware(['ip.whitelist:paymob'])->post('/payment/callback', [PaymentCallbackController::class, 'handle']);



    // إضافة Routes للسائقين
    Route::prefix('driver')->group(function () {
        // التوثيق
        Route::prefix('auth')->group(function () {
            Route::post('/send-otp', [DriverAuthController::class, 'sendOtp']);
            Route::post('/verify-otp', [DriverAuthController::class, 'verifyOtp']);
            Route::post('/register', [DriverAuthController::class, 'register'])->middleware('auth:sanctum');
            Route::post('/complete-profile', [DriverAuthController::class, 'completeProfile']);
            Route::post('/logout', [DriverAuthController::class, 'logout'])->middleware('auth:sanctum');

            // Routes تتطلب مصادقة
            Route::middleware('auth:sanctum')->group(function () {
                Route::get('/profile', [DriverAuthController::class, 'profile']);
                Route::get('/check-registration', [DriverAuthController::class, 'checkRegistration']);
            });
        });

        // الحصول على الدول
        Route::get('/countries', [DriverAuthController::class, 'countries']);

        // // Dashboard للسائق (تتطلب مصادقة وتأكيد السائق)
        Route::middleware(['auth:sanctum', 'driver.only'])->group(function () {
            Route::get('/dashboard', [DriverDashboardController::class, 'index']);
            Route::get('/stats', [DriverDashboardController::class, 'stats']);
            Route::get('/recent-orders', [DriverDashboardController::class, 'recentOrders']);
            Route::get('/earnings', [DriverDashboardController::class, 'earnings']);
        });
    });

    // تحديث Middleware للسائقين
    Route::middleware(['auth:sanctum', 'driver.only'])->prefix('driver')->group(function () {
        Route::post('/orders/{orderId}/accept', [OrderController::class, 'acceptOrder']);
        Route::post('/orders/{orderId}/update-status', [DriverOrderController::class, 'updateStatus']);
        Route::post('/orders/{orderId}/update-location', [DriverOrderController::class, 'updateLocation']);
        Route::get('/orders/{orderId}/path', [DriverOrderController::class, 'getDriverPath']);
        Route::post('/orders/{orderId}/rate-user', [RatingController::class, 'rateUser']);
        Route::post('/offers/{offerId}/cancel', [OrderController::class, 'cancelOffer']);
    });
});
