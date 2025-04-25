<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\FlatController;
use App\Http\Controllers\RentBillController;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/employees', [EmployeeController::class, 'index']);
Route::post('/employees', [EmployeeController::class, 'postData']);
Route::post('/employees/{id}', [EmployeeController::class, 'update']);
Route::delete('/employees/{id}', [EmployeeController::class, 'delete']);

Route::get('/flats', [FlatController::class, 'index']);
Route::post('/flats', [FlatController::class, 'postData']);
Route::post('/flats/{id}', [FlatController::class, 'update']);
Route::delete('/flats/{id}', [FlatController::class, 'delete']);

Route::get('/rentBills', [RentBillController::class, 'index']);
Route::post('/rentBills', [RentBillController::class, 'postData']);
Route::post('/rentBills/{id}', [RentBillController::class, 'update']);
Route::delete('/rentBills/{id}', [RentBillController::class, 'delete']);

