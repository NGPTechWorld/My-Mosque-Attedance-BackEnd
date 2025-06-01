<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Shift;
use App\Models\Attendance;

class DashboardController extends Controller
{
    public function index()
    {
        $totalStudents = Student::count();
        $totalShifts = Shift::count();
        $totalAttendance = Attendance::count();
        $totalAbsence = 0;

        // حساب عدد الغيابات كعدد الأيام الممكنة ناقص عدد الحضور
        $daysCount = Attendance::select('date')->distinct()->count(); // عدد الأيام
        $totalAbsence = $totalStudents * $daysCount - $totalAttendance;

        return view('dashboard', compact('totalStudents', 'totalShifts', 'totalAttendance', 'totalAbsence'));
    }
}
