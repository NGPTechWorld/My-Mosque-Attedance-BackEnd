<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Shift;
use App\Models\PointTransaction;
use App\Services\AttendanceNotifier;
use App\Services\AttendanceRewardService;
use Illuminate\Support\Facades\Http;

class StudentController extends Controller
{
    public function __construct(
        private AttendanceNotifier $notifier,
        private AttendanceRewardService $reward,
    ) {
    }

    public function search(Request $request)
    {
        $query = $request->input('search');

        $students = Student::where('name', 'like', "%{$query}%")->get();

        // أرجع النتائج كـ JSON
        return response()->json($students);
    }
    public function index()
    {
        return Student::with('shift')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|min:1|unique:students,id',
            'code' => 'required|string|unique:students,code',
            'name' => 'required',
            'guardian_phone' => 'required',
            'shift_id' => 'required|exists:shifts,id',
        ]);

        $student = new Student($request->only(['code', 'name', 'guardian_phone', 'shift_id']));
        $student->id = (int) $request->id;   // معرّف يدوي (افتراضياً الرقم التالي)
        $student->save();

        // نرجع للصفحة الرئيسية (مثلاً: students.index) مع رسالة نجاح
        return redirect()->route('students.index')->with('success', 'تم إضافة الطالب بنجاح');
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'code' => 'required|string|unique:students,code,' . $id,
            'name' => 'required|string',
            'guardian_phone' => 'required|string',
            'shift_id' => 'required|exists:shifts,id',
        ]);

        $student = Student::findOrFail($id);
        $student->update([
            'code' => $request->code,
            'name' => $request->name,
            'guardian_phone' => $request->guardian_phone,
            'shift_id' => $request->shift_id,
        ]);

        return redirect()->route('students.index')->with('success', 'تم تعديل بيانات الطالب بنجاح.');
    }



    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('students.index')->with('success', 'تم حذف الطالب بنجاح');
    }


    // إدارة النقاط (من لوحة الويب)
    public function updatePoints(Request $request, $id)
    {
        $request->validate([
            'points' => 'required|integer',
            'reason' => 'nullable|string|max:255',
        ]);

        $student = Student::findOrFail($id);
        $change = (int) $request->input('points');

        if ($student->points + $change < 0) {
            return back()->with('error', 'لا يمكن أن تكون النقاط أقل من صفر');
        }

        $this->recordPointChange($student, $change, $request->input('reason'));

        return back()->with('success', 'تم تحديث النقاط');
    }


    public function updatePointsAPI(Request $request, $id)
    {
        // $id قد يكون الكود اليدوي (من الـ QR) أو الـ id الرقمي
        $student = Student::resolveByCodeOrId($id);
        if (! $student) {
            return response()->json([
                'success' => false,
                'message' => 'الطالب غير موجود',
            ], 404);
        }
        $change = (int) $request->input('points');
        $reason = $request->input('reason');

        if ($student->points + $change < 0) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن أن تكون النقاط أقل من صفر',
            ], 422);
        }

        $this->recordPointChange($student, $change, $reason);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث النقاط بنجاح',
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'points' => $student->points,
            ]
        ]);
    }

    /**
     * تطبيق تغيير على نقاط الطالب وتسجيله في سجل العمليات (المحفظة).
     *
     * @param  Student  $student
     * @param  int      $change  رقم موجب للإضافة، سالب للحذف
     * @param  string|null  $reason
     */
    private function recordPointChange(Student $student, int $change, ?string $reason): void
    {
        $student->addPoints($change, $reason);
    }

    public function showDashboard(Request $request)
    {
        $user = auth()->user();
        $shiftIds = $user->scopedShiftIds();

        // الفترات المتاحة للفلترة (المشرف: فتراته فقط)
        $shiftsQuery = Shift::query();
        if ($shiftIds !== null) {
            $shiftsQuery->whereIn('id', $shiftIds);
        }
        $shifts = $shiftsQuery->get();

        $query = Student::with('shift');

        // المشرف يرى فقط طلاب الفترات المُسندة له
        if ($shiftIds !== null) {
            $query->whereIn('shift_id', $shiftIds);
        }

        // فلتر حسب الفترة المختارة (ضمن المسموح)
        $selectedShift = $request->input('shift_id');
        if ($selectedShift && $user->canAccessShift($selectedShift)) {
            $query->where('shift_id', $selectedShift);
        }

        // بحث بالاسم أو الكود أو الهاتف
        $search = trim((string) $request->input('search'));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('guardian_phone', 'like', "%{$search}%");
            });
        }

        $students = $query->get();
        return view('students.index', compact('students', 'shifts', 'selectedShift', 'search'));
    }

    public function showPoints(Request $request)
    {
        $query = Student::query();

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // المشرف يرى فقط طلاب الفترات المُسندة له
        $shiftIds = auth()->user()->scopedShiftIds();
        if ($shiftIds !== null) {
            $query->whereIn('shift_id', $shiftIds);
        }

        $students = $query->get();
        return view('points.index', compact('students'));
    }

    public function checkInWeb($id)
    {
        $student = Student::with('shift')->findOrFail($id);
        $today = now();

        // يوم الجمعة: الحضور مسموح لكل الطلاب بغضّ النظر عن أيام فترتهم
        if (! $today->isFriday() && !in_array($today->dayOfWeek, $student->shift->days ?? [])) {
            return back()->with('error', 'اليوم ليس من أيام دوام الطالب');
        }

        $already = Attendance::where('student_id', $id)->where('date', $today->toDateString())->exists();
        if ($already) {
            return back()->with('error', 'تم تسجيل الحضور مسبقاً');
        }

        Attendance::create([
            'student_id' => $id,
            'date' => $today->toDateString(),
            'check_in_time' => $today->format('H:i:s'),
        ]);

        // منح نقاط الحضور التلقائية (حسب إعدادات الأدمن)
        $this->reward->applyCheckIn($student, $today);

        // إرسال إشعار Firebase + حفظه في سجل الإشعارات لتطبيق الأهل
        $this->notifier->notify($student, $today);

        return back()->with('success', 'تم تسجيل الحضور بنجاح');
    }

    /**
     * تسجيل غياب الطالب وإرسال إشعار لأهله.
     */
    public function markAbsent($id)
    {
        $student = Student::findOrFail($id);

        // المشرف لا يسجّل غياب طالب خارج فتراته
        if (! auth()->user()->canAccessShift($student->shift_id)) {
            return back()->with('error', 'ليس لديك صلاحية على فترة هذا الطالب.');
        }

        // إن كان الطالب قد سجّل حضوره اليوم لا نسجّله غائباً
        $already = Attendance::where('student_id', $id)
            ->where('date', now()->toDateString())
            ->exists();
        if ($already) {
            return back()->with('error', 'الطالب سجّل حضوره اليوم، لا يمكن تسجيله غائباً.');
        }

        // خصم نقاط الغياب تلقائياً (حسب إعدادات الأدمن)
        $this->reward->applyAbsence($student);

        $this->notifier->notifyAbsence($student, now());

        return back()->with('success', 'تم تسجيل غياب الطالب وإرسال إشعار لأهله.');
    }
   public function edit($id)
{
    $student = Student::findOrFail($id);
    $shifts = Shift::all(); // لعرض كل الفترات في القائمة المنسدلة
    return view('students.edit', compact('student', 'shifts'));
}


    private function sendWhatsAppMessage($phone, $studentName, $time)
    {
        $instanceId = "instance123113";
        $token = "num5sceyvg5215a3";

        $phone = ltrim($phone, '+');

        $message = "السلام عليكم ورحمة الله وبركاته\n";
        $message .= "تم تسجيل دخول الطالب: $studentName\n";
        $message .= "في الساعة " . $time->format('H:i') . "\n\n";
        $message .= "إدارة مسجد الشيخ عبد الغني الغنيمي";

        $response = Http::asForm()->post("https://api.ultramsg.com/{$instanceId}/messages/chat", [
            'token' => $token,
            'to' => $phone,
            'body' => $message,
        ]);

        logger("WhatsApp message sent to {$phone}, response: " . $response->body());
    }


    public function create()
    {
        $shifts = Shift::all();
        // قيمة افتراضية للمعرّف = الرقم التالي المتاح (قابلة للتعديل)
        $defaultId = (int) (Student::max('id') ?? 0) + 1;
        return view('students.create', compact('shifts', 'defaultId'));
    }

    public function show($id)
    {
        $student = Student::with('attendances')->findOrFail($id);
        return view('students.show', compact('student'));
    }

    // عرض/طباعة كود الـ QR الخاص بالطالب
    public function qr($id)
    {
        $student = Student::findOrFail($id);
        return view('students.qr', compact('student'));
    }
}
