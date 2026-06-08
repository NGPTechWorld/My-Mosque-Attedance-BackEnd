<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Shift;
use App\Models\PointTransaction;
use App\Services\AttendanceNotifier;
use Illuminate\Support\Facades\Http;

class StudentController extends Controller
{
    public function __construct(private AttendanceNotifier $notifier)
    {
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
            'code' => 'required|string|unique:students,code',
            'name' => 'required',
            'guardian_phone' => 'required',
            'shift_id' => 'required|exists:shifts,id',
        ]);

        Student::create($request->only(['code', 'name', 'guardian_phone', 'shift_id']));

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
        $student->points += $change;
        $student->save();

        PointTransaction::create([
            'student_id' => $student->id,
            'type' => $change >= 0 ? 'add' : 'remove',
            'amount' => abs($change),
            'reason' => $reason,
            'balance_after' => $student->points,
        ]);
    }

    public function showDashboard()
    {
        $students = Student::with('shift')->get();
        return view('students.index', compact('students'));
    }

    public function showPoints(Request $request)
    {
        $query = Student::query();

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $students = $query->get();
        return view('points.index', compact('students'));
    }

    public function checkInWeb($id)
    {
        $student = Student::with('shift')->findOrFail($id);
        $today = now();
        $dayIndex = $today->dayOfWeek;

        if (!in_array($dayIndex, $student->shift->days ?? [])) {
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

        // إرسال إشعار Firebase + حفظه في سجل الإشعارات لتطبيق الأهل
        $this->notifier->notify($student, $today);

        return back()->with('success', 'تم تسجيل الحضور بنجاح');
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
        return view('students.create', compact('shifts'));
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
