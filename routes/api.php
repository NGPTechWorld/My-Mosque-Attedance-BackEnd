<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TeacherPointController;

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

// الأساتذة
Route::get('/teachers', [TeacherController::class, 'index_api']);
Route::post('/teachers/check-in', [TeacherController::class, 'checkInApi']);

// تطبيق الأساتذة (برنامج النقاط)
Route::post('/teacher/login', [TeacherPointController::class, 'login']);          // دخول الأستاذ
Route::get('/point-reasons', [TeacherPointController::class, 'reasons']);          // أسباب النقاط المفعّلة
Route::post('/teacher/points', [TeacherPointController::class, 'applyPoints']);    // إضافة/حذف نقاط بعد مسح الباركود

// تطبيق الأهل
Route::prefix('parent')->group(function () {
    Route::post('/students/add', [ParentController::class, 'addStudent']);   // دخول / إضافة طالب
    Route::get('/students', [ParentController::class, 'myStudents']);          // قائمة الطلاب حسب رقم الأهل
    Route::get('/students/{id}', [ParentController::class, 'showStudent']);    // بيانات طالب
    Route::get('/students/{id}/notifications', [ParentController::class, 'notifications']);
    Route::get('/students/{id}/points', [ParentController::class, 'points']);  // محفظة النقاط
    Route::get('/notifications', [ParentController::class, 'allNotifications']);
    Route::post('/notifications/{id}/read', [ParentController::class, 'markRead']);
    Route::post('/device', [ParentController::class, 'registerDevice']);       // تسجيل FCM token
});
