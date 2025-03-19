<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\ServicesClientController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\PaymobController;
use App\Http\Controllers\CheckOutController;
use App\Http\Controllers\OrdersCancelController;
use App\Http\Controllers\ClientsCancelController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and assigned to the "web"
| middleware group. Make something great!
|
*/

// Authentication Routes
Route::get('/', function () {
    return view('auth.login');
})->name('login');

Auth::routes(['verify' => true]);

Route::get('getotp', [ForgotPasswordController::class, 'getotp'])->name('getotp');

Route::post('verfiy_email', [ForgotPasswordController::class, 'verfiy_email'])->name('verfiy_email');

Route::get('email', [ForgotPasswordController::class, 'email'])->name('email');
Route::post('sendOtp', [ForgotPasswordController::class, 'sendOtp'])->name('sendOtp');
Route::post('receiveOtp', [ForgotPasswordController::class, 'receiveOtp'])->name('receiveOtp');
Route::post('resetpassword', [ForgotPasswordController::class, 'resetpassword'])->name('resetpassword');




Route::get('thankyou', function () {
    return view('thankyou');
})->name('thankyou');

// Route::get('/' , [CheckOutController::class, 'index']);

//Paymob Routes
Route::post('/credit', [PaymobController::class, 'credit'])->name('checkout'); // this route send all functions data to paymob
Route::get('/callback', [PaymobController::class, 'callback'])->name('callback'); // this route get all reponse data to paymob


Route::get('/getcallback', [PaymobController::class, 'getcallback'])->name('getcallback');




// Authenticated Routes
Route::middleware(['auth:admin_web'])->group(function () {


    Route::post('inactive_order/{id}', [ServicesClientController::class, 'inactive_order'])->name('inactive_order');

    Route::resource('orders_cancel', OrdersCancelController::class);
    Route::resource('clients_cancel', ClientsCancelController::class);




    // Dashboard Routes
    Route::get('home', [HomeController::class, 'index'])->name('home');
    Route::get('users', [HomeController::class, 'users'])->name('users');

    Route::get('orders', [ServicesClientController::class, 'index'])->name('orders');
    Route::get('orders_in_active', [ServicesClientController::class, 'orders_in_active'])->name('orders_in_active');

    Route::get('profile', [HomeController::class, 'profile'])->name('profile');
    Route::get('create', [HomeController::class, 'register'])->name('create');
    Route::post('update_profile', [HomeController::class, 'update_profile'])->name('update_profile');
    Route::post('store_user', [HomeController::class, 'store_user'])->name('store_user');
    Route::resource('dashboard', HomeController::class);

    // Client Routes
    Route::resource('clients', ClientsController::class);
    Route::post('inactive_client/{id}', [ClientsController::class, 'inactive_client'])->name('inactive_client');
    Route::post('active_client/{id}', [ClientsController::class, 'active_client'])->name('active_client');
    Route::get('ClientsInActive', [ClientsController::class, 'ClientsInActive'])->name('clients.inactive');
    Route::post('refund_account/{id}', [ClientsController::class, 'refund_account'])->name('refund_account');



    // Services Client Routes
    Route::resource('show_details', ServicesClientController::class);
    Route::post('refund_service/{id}', [ServicesClientController::class, 'refund_service'])->name('refund_service');

    // User Registration
    Route::post('/register', [AuthController::class, 'register'])->name('register');
});
