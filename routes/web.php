<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Attendance\AttendanceController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\FlatController;
use App\Http\Controllers\RentBillController;
use App\Http\Controllers\AuthController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/attendance', [AttendanceController::class, 'index']);
Route::get('/admin', [AdminController::class, 'index'])->middleware('auth');
Route::get('/admin/employee', [EmployeeController::class, 'home'])->name('employee.home');
Route::get('/admin/flat', [FlatController::class, 'home'])->name('flat.home');
Route::get('/admin/rent/bill', [RentBillController::class, 'home'])->name('rent.home');

Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');