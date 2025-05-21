
<?php

use App\Http\Controllers\AttendanceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;

Route::middleware('api')->group(function () {

    Route::post('/addStudent', [StudentController::class, 'store']);
    Route::get('/students', [StudentController::class, 'index']);
    Route::get('/students/{id}', [StudentController::class, 'show']);
    Route::delete('/deleteStudent/{id}', [StudentController::class, 'destroy']);

});

Route::post('/attendance', [AttendanceController::class, 'markAttendance']);
Route::get('/attendance/{id}', [AttendanceController::class, 'getAttendanceDates']);

Route::get('/test', function () {
    return response()->json([
        'message' => 'API شغال تمام'
    ]);
});
