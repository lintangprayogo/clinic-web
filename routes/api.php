<?php

use App\Http\Controllers\api\DoctorController;
use App\Http\Controllers\api\OrderController;
use App\Http\Controllers\Api\SpecialistController;
use App\Http\Controllers\api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('login', [UserController::class, 'login']);
Route::post('login/google', [UserController::class, 'loginGoogle']);
//doctor routes
Route::get('/doctors', [DoctorController::class, 'index']);
Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::get('/user/check', [UserController::class, 'checkUser']);

    Route::post('/logout', [UserController::class, 'logout']);

    Route::post('/users', [UserController::class, 'store']);

    Route::get('/users/{email}', [UserController::class, 'index']);

    Route::put('/users/googleid/{id}', [UserController::class, 'updateGoogleId']);

    Route::put('/users/{id}', [UserController::class, 'update']);




    Route::post('/doctors', [DoctorController::class, 'store']);

    Route::put('/doctors/{doctor_id}', [DoctorController::class, 'update']);

    Route::delete('/doctors/{doctor_id}', [DoctorController::class, 'destroy']);

    Route::get('/doctors/active', [DoctorController::class, 'getActiveDoctors']);

    Route::get('/doctors/search/', [DoctorController::class, 'searchDoctors']);

    Route::get('/doctors/speacialist/{specialist_id}', [DoctorController::class, 'getDoctorBySpecialist']);

    Route::get('/doctors/clinic/{clinic_id}', [DoctorController::class, 'getDoctorByClinic']);


    //speacialist routes
    Route::get('/specialists', [SpecialistController::class, 'index']);



    //order routes
    Route::post('/orders',[OrderController::class,'store']);

    Route::get('/orders',[OrderController::class,'index']);

    Route::get('/orders/clinic/{clinic_id}',[OrderController::class,'getOrderbyClinic']);

    Route::get('/orders/patient/{patient_id}',[OrderController::class,'getOrderbyPatient']);

    Route::get('/orders/summary/{clinic_id}',[OrderController::class,'getOrderbyDoctor']);


    Route::get('/orders/summary/{clinic_id}',[OrderController::class,'adminClinicSummary']);

});

Route::post('/xendit-callback', [OrderController::class, 'handleCallback']);

