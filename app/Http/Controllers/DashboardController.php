<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Shift;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalStudents = Student::count();
        $totalShifts = Shift::count();
        $totalAttendance = Attendance::count();

        // تقدير عدد الغيابات = (الطلاب × أيام الحضور) - الحضور
        $daysCount = Attendance::select('date')->distinct()->count();
        $totalAbsence = max($totalStudents * $daysCount - $totalAttendance, 0);

        // 1) الحضور خلال آخر 14 يوماً
        $dailyLabels = [];
        $dailyData = [];
        for ($i = 13; $i >= 0; $i--) {
            $day = Carbon::today()->subDays($i);
            $dailyLabels[] = $day->format('m/d');
            $dailyData[] = Attendance::where('date', $day->toDateString())->count();
        }

        // 2) الطلاب والحضور حسب الفترة (هذا الشهر)
        $shifts = Shift::withCount('students')->get();
        $month = now()->format('Y-m');
        $byShift = DB::table('attendances')
            ->join('students', 'attendances.student_id', '=', 'students.id')
            ->where('attendances.date', 'like', $month . '%')
            ->select('students.shift_id as shift_id', DB::raw('count(*) as cnt'))
            ->groupBy('students.shift_id')
            ->pluck('cnt', 'shift_id');

        $shiftLabels = [];
        $shiftStudents = [];
        $shiftAttendance = [];
        foreach ($shifts as $shift) {
            $shiftLabels[] = $shift->name;
            $shiftStudents[] = $shift->students_count;
            $shiftAttendance[] = (int) ($byShift[$shift->id] ?? 0);
        }

        // 3) حضور اليوم: حاضر / غائب
        $presentToday = Attendance::where('date', Carbon::today()->toDateString())
            ->distinct('student_id')->count('student_id');
        $absentToday = max($totalStudents - $presentToday, 0);

        return view('dashboard', compact(
            'totalStudents',
            'totalShifts',
            'totalAttendance',
            'totalAbsence',
            'dailyLabels',
            'dailyData',
            'shiftLabels',
            'shiftStudents',
            'shiftAttendance',
            'presentToday',
            'absentToday'
        ));
    }
}
