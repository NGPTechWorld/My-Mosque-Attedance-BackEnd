<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Attendance;
use Illuminate\Validation\ValidationException;

class AttendanceController extends Controller
{
public function markAttendance(Request $request)
{
    try {
        $validatedData = $request->validate([
            'id' => 'required|integer',
            'attended_at' => 'required|date',
        ]);

        $studentExists = \App\Models\Student::where('id', $validatedData['id'])->exists();
        if (!$studentExists) {
            return response()->json([
                'status' => 'error',
                'message' => 'الطالب غير موجود في النظام',
            ], 404);
        }

    } catch (ValidationException $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'البيانات المرسلة غير صحيحة',
            'errors' => $e->errors(),
        ], 422);
    }

    $studentId = $validatedData['id'];
    $attendedAt = \Carbon\Carbon::parse($validatedData['attended_at']);

    $alreadyMarked = Attendance::where('student_id', $studentId)
        ->whereDate('attended_at', $attendedAt->toDateString())
        ->exists();

    if ($alreadyMarked) {
        return response()->json([
            'status' => 'error',
            'message' => 'تم تسجيل الحضور مسبقاً لهذا اليوم',
        ], 200);
    }

    Attendance::create([
        'student_id' => $studentId,
        'attended_at' => $attendedAt,
    ]);

    return response()->json([
        'status' => 'success',
        'message' => 'تم تسجيل الحضور الساعة ' . $attendedAt->format('H:i'),
    ]);
}
public function getAttendanceDates($studentId)
{
  
    $student = Student::find($studentId);
    if (!$student) {
        return response()->json([
            'status' => 'error',
            'message' => 'الطالب غير موجود',
        ], 404);
    }

   
    $dates = Attendance::where('student_id', $studentId)
        ->orderBy('attended_at', 'desc')
        ->pluck('attended_at')
        ->map(function ($date) {
            if (!$date instanceof \Illuminate\Support\Carbon) {
                $date = \Illuminate\Support\Carbon::parse($date);
            }
            return $date->format('Y-m-d H:i:s');  
        });

    return response()->json([
        'status' => 'success',
        'dates' => $dates,
        'count' => $dates->count(),
    ]);
}


}
