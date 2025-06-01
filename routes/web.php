<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\AttendanceController;

Route::get('/', [StudentController::class, 'showDashboard'])->name('students.index');
Route::post('/students/{id}/checkin', [StudentController::class, 'checkInWeb'])->name('students.checkin');

Route::get('/points', [StudentController::class, 'showPoints'])->name('points.index');
Route::patch('/students/{id}/points', [StudentController::class, 'updatePoints'])->name('students.updatePoints');
Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
Route::get('/students/{id}', [StudentController::class, 'show'])->name('students.show');

Route::get('/shifts', [ShiftController::class, 'index'])->name('shifts.index');
Route::post('/shifts', [ShiftController::class, 'store'])->name('shifts.store');
Route::delete('/shifts/{id}', [ShiftController::class, 'destroy'])->name('shifts.destroy');

Route::get('/attendance/by-shift', [AttendanceController::class, 'byShift'])->name('attendance.byShift');
Route::post('/students', [StudentController::class, 'store'])->name('students.store');
Route::get('/attendance/monthly-report', [AttendanceController::class, 'monthlyReport'])->name('attendance.monthlyReport');
