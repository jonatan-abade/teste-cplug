<?php

use App\Http\Controllers\Api\InventoryController as ApiInventoryController;
use App\Http\Controllers\Api\SaleController as ApiSaleController;
use Illuminate\Support\Facades\Route;

Route::post('/inventory', [ApiInventoryController::class, 'store']);
Route::get('/inventory', [ApiInventoryController::class, 'index']);
Route::post('/sales', [ApiSaleController::class, 'store']);
Route::get('/sales/{id}', [ApiSaleController::class, 'show']);