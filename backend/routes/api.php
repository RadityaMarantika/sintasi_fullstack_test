<?php


use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\VisitController;


Route::get('test-api', function () {
    return response()->json(['message' => 'API Connected']);
});


Route::post('login', [AuthController::class,'login']);
    Route::post('register', function (Request $r) {
});

Route::middleware('auth:sanctum')->group(function(){
    Route::post('logout', [AuthController::class,'logout']);

    Route::apiResource('patients', PatientController::class)->only(['index','store','show']);
    Route::apiResource('visits', VisitController::class)->only(['index','store','show']);
});
