<?php
use App\Http\Controllers\SensorController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


//Public Routes
// Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/sensors', [SensorController::class, 'store']);
Route::put('/sensors/{name}', [SensorController::class, 'update']);


//Protected Routes
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/sensors', [SensorController::class, 'index']);
    Route::delete('/sensors/{name}', [SensorController::class, 'destroy']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/sensors/{name}', [SensorController::class, 'getSensor']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
