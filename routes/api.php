<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\PositionController;
use App\Http\Controllers\Api\SocietyController;
use App\Http\Controllers\Api\PortofolioController;
use App\Http\Controllers\Api\PositionAppliedController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/hrd/profilehrd', [AuthController::class, 'ProfileHRD']);
    Route::post('/society/profile', [AuthController::class, 'ProfileSociety']);
});

Route::middleware('auth:sanctum')->prefix('companies')->group(function () {
    Route::post('/create', [CompanyController::class, 'create']);
    Route::get('/read', [CompanyController::class, 'read']);
    Route::get('/read/{id}', [CompanyController::class, 'readById']);
    Route::get('/read-me', [CompanyController::class, 'readMe']);
    Route::post('/update-me', [CompanyController::class, 'updateMe']);
});

Route::middleware('auth:sanctum')->prefix('societies')->group(function () {
    Route::post('/create', [SocietyController::class, 'create']);
    Route::get('/read', [SocietyController::class, 'read']);
    Route::get('/read/{id}', [SocietyController::class, 'readById']);
    Route::get('/read-me', [SocietyController::class, 'readMe']);
    Route::post('/update-me', [SocietyController::class, 'updateMe']);
});

Route::middleware('auth:sanctum')->prefix('hrd')->group(function () {
    Route::post('/insert', [PositionController::class, 'insertPosition']);
    Route::post('/update/{id}', [PositionController::class, 'updatePosition']);
    Route::get('/get', [PositionController::class, 'getPosition']);
    Route::delete('/delete/{id}', [PositionController::class, 'deletePosition']);
});

Route::middleware('auth:sanctum')->prefix('society')->group(function () {
    Route::post('/portofolio', [PortofolioController::class, 'insertportofolio']);
    Route::get('/portofolio', [PortofolioController::class, 'myPortofolio']);
    Route::post('/portofolio/{id}', [PortofolioController::class, 'update']);
    Route::delete('/portofolio/{id}', [PortofolioController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->prefix('applied')->group(function () {
    Route::post('/create', [PositionAppliedController::class, 'apply']);
    Route::get('/by-position/{id}', [PositionAppliedController::class, 'readByAvailablePosition']);
    Route::get('/my', [PositionAppliedController::class, 'readMyApplications']);
    Route::post('/update-status/{id}', [PositionAppliedController::class, 'updateStatus']);
    Route::delete('/delete/{id}', [PositionAppliedController::class, 'delete']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});