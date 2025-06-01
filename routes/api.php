<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\AttendanceController;

// الطلاب
Route::get('/students', [StudentController::class, 'index']);
Route::post('/students', [StudentController::class, 'store']);
Route::put('/students/{id}', [StudentController::class, 'update']);
Route::delete('/students/{id}', [StudentController::class, 'destroy']);
Route::post('/students/{id}/points', [StudentController::class, 'updatePointsAPI'])->name('students.updatePoints');


// الفترات
Route::get('/shifts', [ShiftController::class, 'index']);
Route::post('/shifts', [ShiftController::class, 'store']);
Route::put('/shifts/{id}', [ShiftController::class, 'update']);
Route::delete('/shifts/{id}', [ShiftController::class, 'destroy']);

// الحضور
Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn']);
Route::get('/attendance', [AttendanceController::class, 'index']);
