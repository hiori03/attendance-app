<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\ListController as AdminListController;
use App\Http\Controllers\Admin\RequestController;
use App\Http\Controllers\Staff\AttendanceController;
use App\Http\Controllers\Staff\ListController as StaffListController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/register', [AuthController::class, 'registerform']);

Route::get('/admin/attendance/list', [AdminListController::class, 'attendance_listform']);

Route::get('/attendance', [AttendanceController::class, 'attendanceform']);