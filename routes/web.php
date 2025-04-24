<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Attendance\AttendanceController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\Customer\CustomerController;


// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/attendance', [AttendanceController::class, 'index']);
Route::get('/', [AdminController::class, 'index']);
Route::get('/admin/employee', [EmployeeController::class, 'home'])->name('employee.home');
Route::get('/admin/customer', [CustomerController::class, 'home'])->name('customer.home');
