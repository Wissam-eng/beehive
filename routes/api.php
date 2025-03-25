<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\ServicesClientController;
use App\Http\Controllers\OrdersCancelController;
use App\Http\Controllers\ClientsCancelController;
use App\Http\Controllers\Auth\ForgotPasswordController;

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


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('loginWithJWT', [LoginController::class, 'loginWithJWT']);
Route::post('/logout', [AuthController::class, 'logout']);



//----------------RESET PASSWORD & VERFIY EMAIL-----------------------------
Route::post('sendOtp', [ForgotPasswordController::class, 'sendOtp_api'])->name('sendOtp');
Route::post('checkOtp', [ForgotPasswordController::class, 'receiveOtp_api'])->name('checkOtp');
Route::post('resetpassword', [ForgotPasswordController::class, 'resetpassword_api'])->name('resetpassword');
Route::post('verfiy_email', [ForgotPasswordController::class, 'verfiy_email_api'])->name('verfiy_email');





Route::middleware(['auth:admins'])->group(function () {});



Route::middleware(['auth:clients'])->group(function () {



    Route::post('update', [ClientsController::class, 'update'])->name('update');




    Route::post('clients_cancel', [ClientsCancelController::class, 'store'])->name('clients_cancel');
    Route::get('clients_cancel', [ClientsCancelController::class, 'index_api'])->name('clients_cancel');



    Route::post('orders_cancel/{id}', [OrdersCancelController::class, 'store'])->name('orders_cancel');
    Route::get('orders_cancel', [OrdersCancelController::class, 'index_api'])->name('orders_cancel');




    Route::post('inactive_order/{id}', [ServicesClientController::class, 'inactive_order'])->name('inactive_order');


    Route::get('show_my_order/{id}', [ServicesClientController::class, 'show_my_order'])->name('show_my_order');


    Route::get('show_all_my_order', [ServicesClientController::class, 'show_all_my_order'])->name('show_all_my_order');

    Route::post('inactive_my_account/{id}', [ClientsController::class, 'inactive_my_account'])->name('inactive_my_account');

    Route::get('show_my_account', [ClientsController::class, 'show_my_account'])->name('show_my_account');

    Route::post('add_service', [ClientsController::class, 'add_service'])->name('add_service');
});


Route::post('register_client', [ClientsController::class, 'store'])->name('register_client');
