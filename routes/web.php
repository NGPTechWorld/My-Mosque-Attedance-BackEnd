<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\SupervisorController;


Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


Route::middleware(['auth', 'section'])->group(function () {
Route::get('/', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');
Route::get('/students', [StudentController::class, 'showDashboard'])->name('students.index');
Route::post('/students/{id}/checkin', [StudentController::class, 'checkInWeb'])->name('students.checkin');
Route::post('/students/{id}/absent', [StudentController::class, 'markAbsent'])->name('students.absent');
Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');

Route::get('/points', [StudentController::class, 'showPoints'])->name('points.index');
Route::patch('/students/{id}/points', [StudentController::class, 'updatePoints'])->name('students.updatePoints');
Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
Route::get('/students/{id}/qr', [StudentController::class, 'qr'])->name('students.qr');
Route::get('/students/{id}', [StudentController::class, 'show'])->name('students.show');
Route::get('/students/search', [StudentController::class, 'search']);
Route::get('students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');

Route::get('/shifts', [ShiftController::class, 'index'])->name('shifts.index');
Route::post('/shifts', [ShiftController::class, 'store'])->name('shifts.store');
Route::delete('/shifts/{id}', [ShiftController::class, 'destroy'])->name('shifts.destroy');

Route::get('/attendance/by-shift', [AttendanceController::class, 'byShift'])->name('attendance.byShift');
Route::post('/students', [StudentController::class, 'store'])->name('students.store');
Route::get('/attendance/monthly-report', [AttendanceController::class, 'monthlyReport'])->name('attendance.monthlyReport');
Route::get('/attendance/friday', [AttendanceController::class, 'fridayReport'])->name('attendance.friday');

// إدارة الأساتذة
Route::get('/teachers', [TeacherController::class, 'index'])->name('teachers.index');
Route::get('/teachers/create', [TeacherController::class, 'create'])->name('teachers.create');
Route::post('/teachers', [TeacherController::class, 'store'])->name('teachers.store');
Route::get('/teachers/report', [TeacherController::class, 'report'])->name('teachers.report');
Route::get('/teachers/{id}/qr', [TeacherController::class, 'qr'])->name('teachers.qr');
Route::get('/teachers/{id}/edit', [TeacherController::class, 'edit'])->name('teachers.edit');
Route::put('/teachers/{id}', [TeacherController::class, 'update'])->name('teachers.update');
Route::delete('/teachers/{id}', [TeacherController::class, 'destroy'])->name('teachers.destroy');
Route::post('/teachers/{id}/checkin', [TeacherController::class, 'checkInWeb'])->name('teachers.checkin');

// متابعة النظام (تفاعل تطبيق الأهل)
Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring.index');

// إرسال إعلان/إشعار لأهالي دوام مختار
Route::get('/announcements', [AnnouncementController::class, 'create'])->name('announcements.create');
Route::post('/announcements/send', [AnnouncementController::class, 'send'])->name('announcements.send');

// إعدادات نقاط الحضور
Route::get('/settings/attendance-reward', [SettingsController::class, 'attendanceReward'])->name('settings.attendanceReward');
Route::post('/settings/attendance-reward', [SettingsController::class, 'updateAttendanceReward'])->name('settings.attendanceReward.update');

// إدارة المشرفين (للمدير العام فقط)
Route::get('/supervisors', [SupervisorController::class, 'index'])->name('supervisors.index');
Route::get('/supervisors/create', [SupervisorController::class, 'create'])->name('supervisors.create');
Route::post('/supervisors', [SupervisorController::class, 'store'])->name('supervisors.store');
Route::get('/supervisors/{user}/edit', [SupervisorController::class, 'edit'])->name('supervisors.edit');
Route::put('/supervisors/{user}', [SupervisorController::class, 'update'])->name('supervisors.update');
Route::delete('/supervisors/{user}', [SupervisorController::class, 'destroy'])->name('supervisors.destroy');
});


