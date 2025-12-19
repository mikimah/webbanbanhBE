<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Authcontroller\AuthController;
use App\Http\Controllers\DanhMucController;
use App\Http\Controllers\SanPhamController;
use App\Http\Controllers\DonHangController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('register', [AuthController::class, 'user_register']);
Route::post('login', [AuthController::class, 'user_login']);
Route::post('logout', [AuthController::class, 'user_logout'])->middleware('auth:sanctum');

Route::post('/category/add', [DanhMucController::class, 'add'])->middleware(['auth:sanctum','VaiTro:admin']);
Route::get('/category', [DanhMucController::class, 'getAll']);
Route::get('/category/{id}', [DanhMucController::class, 'getById']);
Route::get('/category/name/{name}', [DanhMucController::class, 'getByName']);
Route::delete('/category/{id}', [DanhMucController::class, 'delete'])->middleware(['auth:sanctum','VaiTro:admin']);
Route::post('/category/{id}', [DanhMucController::class, 'update'])->middleware(['auth:sanctum','VaiTro:admin']);


Route::post('/product',[SanPhamController::class,'add'])->middleware(['auth:sanctum','VaiTro:admin']);
Route::get('/product', [SanPhamController::class, 'getAll']);
Route::get('/product/{id}', [SanPhamController::class, 'getById']);
Route::get('/product/name/{name}', [SanPhamController::class, 'getByName']);
Route::get('/product/cate/{id}', [SanPhamController::class, 'getByCate']);
Route::delete('/product/{id}', [SanPhamController::class, 'delete'])->middleware(['auth:sanctum','VaiTro:admin']);
Route::post('/product/{id}', [SanPhamController::class, 'update'])->middleware(['auth:sanctum','VaiTro:admin']);

Route::post('/order',[DonHangController::class,'add']);
Route::get('/order', [DonHangController::class, 'getAll'])->middleware(['auth:sanctum','VaiTro:admin']);
Route::get('/order/search', [DonHangController::class, 'getByDate'])->middleware(['auth:sanctum','VaiTro:admin']);


