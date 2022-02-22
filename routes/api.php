<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Modules\Banking\Controllers\AccountController;
use App\Http\Controllers\AuthenticationController;
use App\Modules\Banking\Controllers\TransferController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::post('/register', [AuthenticationController::class, 'register']);
Route::post('/login', [AuthenticationController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::get('/profile', function (Request $request) {
        return $request->user();
    });
    Route::post('/log-out', [AuthenticationController::class, 'logout']);

    Route::name('account.')->group(function () {
        Route::get('/accounts', [AccountController::class, 'index'])->name('inedx');
        Route::post('/accounts', [AccountController::class, 'store'])->name('store');
        Route::get('/accounts/{account}', [AccountController::class, 'show'])->name('show');
        Route::patch('/accounts/{account}', [AccountController::class, 'update'])->name('update');
        Route::patch('/accounts/{account}/add-credit', [AccountController::class, 'addCredit'])->name('add_credit');
        Route::delete('/accounts/{account}', [AccountController::class, 'destroy'])->name('destroy');
    });

    Route::name('transfer.')->group(function () {
        Route::get('/transfers/{transfer}', [TransferController::class, 'show'])->name('show');
        Route::get('/transfers', [TransferController::class, 'index'])->name('index');
        Route::get('/transfers/account/{account}', [TransferController::class, 'index'])->name('account_index');
        Route::post('/transfers/account/{account}', [TransferController::class, 'store'])->name('store');
    });


});




