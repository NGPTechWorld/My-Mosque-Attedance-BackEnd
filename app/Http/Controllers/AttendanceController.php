<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Attendance;
use App\Models\Teacher;
use App\Services\AttendanceNotifier;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use App\Models\Shift;
use Illuminate\Support\Facades\Log;

use Illuminate\Validation\ValidationException;

class AttendanceController extends Controller
{
    public function __construct(private AttendanceNotifier $notifier)
    {
    }

    public function monthlyReport(Request $request)
    {
        $shiftId = $request->input('shift_id');
        $month = $request->input('month', now()->format('Y-m')); // الشكل: 2025-06

        $shifts = Shift::all();
        $students = collect();
        $dates = [];

        if ($shiftId) {
            $students = Student::with(['attendances' => function ($q) use ($month) {
                $q->where('date', 'like', $month . '%');
            }])->where('shift_id', $shiftId)->get();

            // إنشاء قائمة أيام الشهر
            $start = Carbon::parse($month . '-01');
            $end = $start->copy()->endOfMonth();
            while ($start <= $end) {
                $dates[] = $start->copy();
                $start->addDay();
            }
        }

        return view('attendance.monthly_report', compact('shifts', 'students', 'dates', 'shiftId', 'month'));
    }
    // تقرير حضور يوم الجمعة (مأخوذ من نسخة attendance-system-server)
    public function fridayReport(Request $request)
    {
        $date = $request->input('date', now()->next(Carbon::FRIDAY)->toDateString());

        $students = Student::with(['attendances' => function ($q) use ($date) {
            $q->where('date', $date);
        }])->get();

        return view('attendance.friday_report', compact('students', 'date'));
    }

    public function byShift(Request $request)
    {
        $shifts = Shift::all();
        $students = null;
        $teachers = null;

        if ($request->shift_id && $request->date) {
            $students = Student::with(['attendances' => function ($q) use ($request) {
                $q->where('date', $request->date);
            }])->where('shift_id', $request->shift_id)->get();

            // أساتذة نفس الفترة وحضورهم بنفس التاريخ
            $teachers = Teacher::with(['attendances' => function ($q) use ($request) {
                $q->where('date', $request->date);
            }])->where('shift_id', $request->shift_id)->get();
        }

        return view('attendance.by_shift', compact('shifts', 'students', 'teachers'));
    }


    // public function checkIn(Request $request)
    // {
    //     try {
    //         // التحقق من صحة البيانات مع قواعد التحقق
    //         $request->validate([
    //             'student_id' => 'required|exists:students,id',
    //             'date' => 'nullable|date',
    //         ]);

    //         $student = Student::with('shift')->findOrFail($request->student_id);

    //         $today = $request->date ? Carbon::parse($request->date) : Carbon::now();
    //         $dayIndex = $today->dayOfWeek;
    //         $shift = $student->shift;

    //         if (!in_array($dayIndex, $shift->days)) {
    //             return response()->json(['message' => 'اليوم ليس من أيام دوام الطالب.'], 400);
    //         }

    //         $alreadyChecked = Attendance::where('student_id', $student->id)
    //             ->where('date', $today->toDateString())
    //             ->exists();

    //         if ($alreadyChecked) {
    //             return response()->json(['message' => 'تم تسجيل الحضور مسبقاً.'], 400);
    //         }

    //         $attendance = Attendance::create([
    //             'student_id' => $student->id,
    //             'date' => $today->toDateString(),
    //             'check_in_time' => $today->format('H:i:s'),
    //         ]);

    //         try {
    //             $this->sendWhatsAppMessage($student->guardian_phone, $student->name, $today);
    //         } catch (\Exception $e) {
    //             Log::error("Failed to send WhatsApp message: " . $e->getMessage());
    //         }

    //         return response()->json([
    //             'message' => 'تم تسجيل الحضور بنجاح.',
    //             'attendance' => $attendance,
    //             'date' => $today->toDateString(),
    //             'time' => $today->format('H:i:s'),
    //         ]);

    //     } catch (ValidationException $ve) {
    //         // نرجع رسالة الخطأ الأولى فقط من التحقق
    //         $errors = $ve->validator->errors()->all();
    //         return response()->json(['message' => $errors[0] ?? 'خطأ في البيانات المدخلة.'], 422);
    //     } catch (\Exception $e) {
    //         Log::error("Error in checkIn: " . $e->getMessage());
    //         return response()->json(['message' => 'حدث خطأ غير متوقع، يرجى المحاولة لاحقاً.'], 500);
    //     }
    // }



    public function checkIn(Request $request)
    {
        try {
            $request->validate([
                'student_id' => 'required',
                'date' => 'nullable|date',
            ]);

            // القيمة الممسوحة من الـ QR قد تكون الكود اليدوي أو الـ id
            $student = Student::resolveByCodeOrId($request->student_id);
            if (! $student) {
                return response()->json(['message' => 'الطالب غير موجود.'], 404);
            }
            $student->load('shift');
            $shift = $student->shift;

            $today = $request->date ? Carbon::parse($request->date) : Carbon::now();
            $dayIndex = $today->dayOfWeek;

            if (!in_array($dayIndex, $shift->days)) {
                return response()->json(['message' => 'اليوم ليس من أيام دوام الطالب.'], 400);
            }

            // التحقق من الوقت ضمن الفترة الزمنية للدوام
            $currentTime = $today->format('H:i:s');
            $startTime = $shift->start_time; // مثال: "08:00:00"
            $endTime = $shift->end_time;     // مثال: "14:00:00"

            if ($currentTime < $startTime || $currentTime > $endTime) {
                return response()->json(['message' => 'غير مسموح بتسجيل الحضور خارج فترة الدوام.'], 400);
            }

            $alreadyChecked = Attendance::where('student_id', $student->id)
                ->where('date', $today->toDateString())
                ->exists();

            if ($alreadyChecked) {
                return response()->json(['message' => 'تم تسجيل الحضور مسبقاً.'], 400);
            }

            $attendance = Attendance::create([
                'student_id' => $student->id,
                'date' => $today->toDateString(),
                'check_in_time' => $currentTime,
            ]);

            // إنشاء سجل إشعار + إرسال إشعار Firebase لتطبيق الأهل
            $this->notifier->notify($student, $today);

            return response()->json([
                'message' => 'تم تسجيل الحضور بنجاح.',
                'attendance' => $attendance,
                'date' => $today->toDateString(),
                'time' => $currentTime,
            ]);
        } catch (ValidationException $ve) {
            $errors = $ve->validator->errors()->all();
            return response()->json(['message' => $errors[0] ?? 'خطأ في البيانات المدخلة.'], 422);
        } catch (\Exception $e) {
            Log::error("Error in checkIn: " . $e->getMessage());
            return response()->json(['message' => 'حدث خطأ غير متوقع، يرجى المحاولة لاحقاً.'], 500);
        }
    }

    // عرض حضور طلاب فترة محددة
    public function index(Request $request)
    {
        $request->validate([
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from',
        ]);

        $attendances = Attendance::with('student')
            ->whereBetween('date', [$request->from, $request->to])
            ->get();

        return response()->json($attendances);
    }
}
