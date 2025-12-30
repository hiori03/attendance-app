<?php

use App\Http\Controllers\Admin\ListController as AdminListController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Staff\AttendanceController;
use App\Http\Controllers\Staff\ListController as StaffListController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::get('/register', [AuthController::class, 'registerForm'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::get('/login', [AuthController::class, 'loginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/admin/login', [AuthController::class, 'adminLoginForm'])->name('admin.login.form');
Route::post('/admin/login', [AuthController::class, 'adminLogin'])->name('admin.login');
Route::post('/admin/logout', [AuthController::class, 'adminLogout'])->name('admin.logout');

Route::get('/email', [AuthController::class, 'emailForm'])->name('email');
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'certification'])->middleware(['signed'])->name('email.certification');
Route::post('/email/resend', [AuthController::class, 'resend'])->name('email.resend');

Route::middleware('auth')->group(function () {
    Route::get('/admin/attendance/list', [AdminListController::class, 'attendanceListForm'])->name('admin.attendance.list.form');
    Route::get('/admin/staff/list', [AdminListController::class, 'staffListForm'])->name('admin.staff.list.form');

    Route::get('/attendance', [AttendanceController::class, 'attendanceForm'])->name('attendance.form');
    Route::post('/attendance/start', [AttendanceController::class, 'attendanceStart'])->name('attendance.start');
    Route::post('/attendance/end', [AttendanceController::class, 'attendanceEnd'])->name('attendance.end');
    Route::post('/attendance/break/start', [AttendanceController::class, 'breakStart'])->name('break.start');
    Route::post('/attendance/break/end', [AttendanceController::class, 'breakEnd'])->name('break.end');

    Route::get('/attendance/list', [StaffListController::class, 'attendanceListForm'])->name('attendance.list.form');
    Route::post('/attendance/list/change', [StaffListController::class, 'changeMonth'])->name('attendance.list.changeMonth');

    Route::post('/attendance/detail/prepare', [StaffListController::class, 'prepareDetail'])->name('attendance.detail.prepare');
    Route::get('/attendance/detail/{id}', [StaffListController::class, 'attendanceDetailForm'])->name('attendance.detail.form');
    Route::post('/attendance/detail/request',[StaffListController::class, 'DetailRequest'])->name('attendance.detail.request');

    Route::middleware(['auth', 'request.list'])->get('/stamp_correction_request/list', function (Request $request) {

        $controller = $request->get('controller');

        return app($controller)->requestForm($request);

    })->name('stamp_correction_request.form');
});