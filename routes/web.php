<?php


use App\Mail\hellomail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Models\admins;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ServicesClientController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

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

// Route::get('/', function () {
//     // Mail::to('wissamomran8@gmail.com')->send(new \App\Mail\hellomail());
//     return view('welcome');
// });

Route::get('/', function () {
    return view('auth.login');
})->name('login');




Route::middleware(['auth:web'])->group(function () {


    // Route::get('show_details/{id}', [ClientsController::class, 'show_details'])->name('show_details');


    Route::get('create', [HomeController::class, 'register'])->name('create');


    Route::get('profile', [HomeController::class, 'profile'])->name('profile');


    Route::post('update_profile', [HomeController::class, 'update_profile'])->name('update_profile');

    Route::post('store_user', [HomeController::class, 'store_user'])->name('store_user');

    Route::get('home', [HomeController::class, 'index'])->name('home');



    Route::post('inactive_client/{id}', [ClientsController::class, 'inactive_client'])->name('inactive_client');
    Route::post('active_client/{id}', [ClientsController::class, 'active_client'])->name('active_client');

    Route::resource('dashboard', HomeController::class);
    // Dashboard Routes

    // Clients Routes
    Route::resource('clients', ClientsController::class);
    Route::resource('show_details', ServicesClientController::class);




    Route::get('ClientsInActive', [ClientsController::class, 'ClientsInActive'])->name('clients.inactive');

    // Home and User Management
    Route::get('users', [HomeController::class, 'users'])->name('users');
    Route::get('orders', [HomeController::class, 'orders'])->name('orders');
    Route::get('profile', [HomeController::class, 'profile'])->name('profile');
    Route::get('register_user', [HomeController::class, 'register'])->name('register_user');

    // Auth Routes
    Route::post('/register', [AuthController::class, 'register'])->name('register');
});


Route::get('email', [ForgotPasswordController::class, 'email'])->name('email');
// Route::get('otpcode', [ForgotPasswordController::class, 'otpcode'])->name('otpcode');
Route::post('sendOtp', [ForgotPasswordController::class, 'sendOtp'])->name('sendOtp');
Route::post('receiveOtp', [ForgotPasswordController::class, 'receiveOtp'])->name('receiveOtp');
Route::post('resetpassword', [ForgotPasswordController::class, 'resetpassword'])->name('resetpassword');













// Route::get('otp', function () {
//     return view('otp.index');
// })->name('otp');

// Route::post('/otp-check', function (Request $request) {
//     $user = admins::where('email', $request->email)->first();

//     if ($user && $user->otp == $request->otp) {
//         // OTP verified successfully
//         // return redirect()->route('dashboard');
//         return view('welcome');
//     } else {
//         // Invalid OTP
//         return back()->withErrors(['otp' => 'Invalid OTP. Please try again.']);
//     }
// })->name('otp.check');

Auth::routes();
