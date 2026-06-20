<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Attendance;
use App\Models\Shift;
use App\Models\Student;
use App\Services\AttendanceNotifier;
use App\Services\AttendanceRewardService;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * تسجيل الغياب: اختيار الفترة والتاريخ، عرض الطلاب وحالتهم (حاضر/غائب/غير مسجّل)،
 * وتحديد غياب مبرّر أو غير مبرّر لطالب أو عدّة طلاب دفعة واحدة.
 */
class AbsenceController extends Controller
{
    public function __construct(
        private AttendanceNotifier $notifier,
        private AttendanceRewardService $reward,
    ) {
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $shiftIds = $user->scopedShiftIds();

        $shiftsQuery = Shift::query();
        if ($shiftIds !== null) {
            $shiftsQuery->whereIn('id', $shiftIds);
        }
        $shifts = $shiftsQuery->get();

        $selectedShift = $request->input('shift_id');
        $date = $request->input('date', now()->toDateString());
        $students = collect();

        if ($selectedShift && $user->canAccessShift($selectedShift)) {
            $list = Student::where('shift_id', $selectedShift)->orderBy('name')->get();
            $ids = $list->pluck('id');

            $presentIds = Attendance::where('date', $date)->whereIn('student_id', $ids)
                ->pluck('student_id')->all();
            $absenceTypes = Absence::where('date', $date)->whereIn('student_id', $ids)
                ->pluck('type', 'student_id');

            $students = $list->map(function ($s) use ($presentIds, $absenceTypes) {
                if (in_array($s->id, $presentIds)) {
                    $s->status = 'present';
                } else {
                    $s->status = $absenceTypes[$s->id] ?? 'none'; // excused | unexcused | none
                }
                return $s;
            });
        }

        return view('absences.index', compact('shifts', 'selectedShift', 'date', 'students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'shift_id' => 'required|exists:shifts,id',
            'date' => 'required|date',
            'type' => 'required|in:excused,unexcused',
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'integer',
        ], [
            'student_ids.required' => 'الرجاء اختيار طالب واحد على الأقل.',
        ]);

        $user = auth()->user();
        if (! $user->canAccessShift($request->shift_id)) {
            abort(403, 'ليس لديك صلاحية على هذه الفترة.');
        }

        $date = $request->date;
        $type = $request->type;

        $students = Student::whereIn('id', $request->student_ids)
            ->where('shift_id', $request->shift_id)
            ->get();

        $count = 0;
        foreach ($students as $student) {
            // لا نسجّل غياب طالب حاضر
            $present = Attendance::where('student_id', $student->id)->where('date', $date)->exists();
            if ($present) {
                continue;
            }

            $absence = Absence::firstOrNew(['student_id' => $student->id, 'date' => $date]);
            $isNew = ! $absence->exists;
            $absence->type = $type;
            $absence->save();

            // الخصم والإشعار يتمّان عند أول تسجيل فقط
            if ($isNew) {
                // الغياب المبرّر فقط يُخصم منه نقاط
                if ($type === 'excused') {
                    $this->reward->applyAbsence($student);
                }
                $this->notifier->notifyAbsence($student, Carbon::parse($date), $type);
                $count++;
            }
        }

        $label = Absence::TYPES[$type] ?? $type;
        return back()->with('success', "تم تسجيل غياب ({$label}) لـ {$count} طالب وإرسال إشعار لأهلهم.");
    }
}
