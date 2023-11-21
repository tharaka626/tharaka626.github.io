<?php

use App\Http\Controllers\CustomerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceItemController;
use App\Http\Controllers\ProductController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Invoice routes
Route::apiResource('invoices', InvoiceController::class);
Route::get('/search_invoices', [InvoiceController::class, 'search_invoice']);
Route::get('/create_invoice', [InvoiceController::class, 'create_invoice']);
Route::post('/update_invoice/{id}', [InvoiceController::class, 'update']);


//Invoice Items routes
Route::apiResource('invoice_items', InvoiceItemController::class);

//Customer routes
Route::apiResource('customers', CustomerController::class);

//Product routes
Route::apiResource('products', ProductController::class);


