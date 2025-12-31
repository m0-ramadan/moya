<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\SliderController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\ContactUsController;
use App\Http\Controllers\Api\StaticPagesController;
use App\Http\Controllers\Api\Website\HomeController;
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
    });
    Route::get('/services', [ServiceController::class, 'index']);
    Route::get('/sliders', [SliderController::class, 'index']);

    Route::prefix('auth')->group(function () {
        Route::post('/send-otp', [AuthController::class, 'sendOtp']);
        Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
        Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
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

    // أقسام المقالات
    Route::prefix('categories')->group(function () {
        Route::get('/', [ArticleCategoryController::class, 'index']);
        Route::get('/{slug}', [ArticleCategoryController::class, 'show']);
        Route::get('/{slug}/articles', [ArticleCategoryController::class, 'articles']);
    });

    // تعليقات المقالات
    Route::prefix('articles/{articleId}/comments')->group(function () {
        Route::get('/', [ArticleCommentController::class, 'index']);
        Route::post('/', [ArticleCommentController::class, 'store']);

        // إعجاب بالتعليق (تتطلب مصادقة)
        Route::middleware('auth:sanctum')->post('/{commentId}/like', [ArticleCommentController::class, 'like']);
    });

    // التعليقات العامة
    Route::prefix('comments')->middleware('auth:sanctum')->group(function () {
        Route::post('/{commentId}/like', [ArticleCommentController::class, 'like']);
    });

    Route::post('contact-us', [ContactUsController::class, 'store']);

    // إنشاء طلب
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);

    // حفظ عنوان
    Route::post('/locations', [SavedLocationController::class, 'store']);
});
