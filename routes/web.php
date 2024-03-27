<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
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

Route::redirect('/', '/products');
Route::resource('products', ProductController::class);
Route::post('product_file_upload',[ProductController::class,'fileUpload']);
Route::get('product_data_table', [ProductController::class, 'tableData'])->name('product_data_table');
