<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('website.home');

// Route::get('/privacy-police', function () {
//     return view('website.pages.privacy');
// })->name('website.privacy');

// Route::get('/products', function () {
//     return view('website.pages.privacy');
// })->name('website.products');

// Route::get('/product/{id}', function () {
//     return view('website.pages.product.product');
// })->name('website.product');

// Route::get('/unreachable-product', function () {
//     return view('website.pages.product.unreachable');
// })->name('website.product.unreachable');

// Route::get('/f-a-q', function () {
//     return view('website.pages.faq');
// })->name('website.faq');

