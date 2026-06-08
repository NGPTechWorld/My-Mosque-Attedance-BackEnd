<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\DeviceToken;
use App\Models\ParentNotification;
use Illuminate\Http\Request;

/**
 * واجهة API الخاصة بتطبيق الأهل (Flutter).
 *
 * نموذج الدخول: الأهل يفوتون باسم الطالب + رقم الأهل (ككلمة سر).
 * بما أن جميع أبناء نفس الأهل يحملون نفس رقم الأهل، يمكن للأهل إضافة أكثر من طالب.
 * الأمان: كل طلب يخص طالباً يتحقق من تطابق رقم الأهل مع guardian_phone للطالب.
 */
class ParentController extends Controller
{
    /**
     * تسجيل/إضافة طالب: التحقق من الاسم + رقم الأهل وإرجاع بيانات الطالب.
     * تُستخدم لأول دخول ولإضافة طلاب آخرين.
     */
    public function addStudent(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string',
            'fcm_token' => 'nullable|string',
            'platform' => 'nullable|string',
        ]);

        $student = Student::with('shift')
            ->where('name', $request->name)
            ->where('guardian_phone', $request->phone)
            ->first();

        if (! $student) {
            return response()->json([
                'success' => false,
                'message' => 'الاسم أو رقم الأهل غير صحيح.',
            ], 404);
        }

        // تسجيل جهاز الأهل لاستقبال الإشعارات
        if ($request->filled('fcm_token')) {
            $this->saveDevice($request->fcm_token, $request->phone, $request->platform);
        }

        return response()->json([
            'success' => true,
            'student' => $this->studentPayload($student),
        ]);
    }

    /**
     * جلب كل طلاب الأهل المرتبطين برقم الهاتف (لتحديث القائمة عند فتح التطبيق).
     */
    public function myStudents(Request $request)
    {
        $request->validate(['phone' => 'required|string']);

        $students = Student::with('shift')
            ->where('guardian_phone', $request->phone)
            ->get()
            ->map(fn ($s) => $this->studentPayload($s));

        return response()->json([
            'success' => true,
            'students' => $students,
        ]);
    }

    /**
     * بيانات طالب واحد محدّثة.
     */
    public function showStudent(Request $request, $id)
    {
        $student = $this->authorizeStudent($request, $id);
        if (! $student) {
            return $this->forbidden();
        }

        return response()->json([
            'success' => true,
            'student' => $this->studentPayload($student),
        ]);
    }

    /**
     * سجل الإشعارات لطالب محدّد.
     */
    public function notifications(Request $request, $id)
    {
        $student = $this->authorizeStudent($request, $id);
        if (! $student) {
            return $this->forbidden();
        }

        $notifications = $student->notifications()->limit(100)->get();

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
        ]);
    }

    /**
     * سجل إشعارات كل أبناء الأهل (تغذية موحّدة).
     */
    public function allNotifications(Request $request)
    {
        $request->validate(['phone' => 'required|string']);

        $notifications = ParentNotification::with('student:id,name')
            ->where('guardian_phone', $request->phone)
            ->latest()
            ->limit(200)
            ->get();

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
        ]);
    }

    /**
     * تعليم إشعار كمقروء.
     */
    public function markRead(Request $request, $id)
    {
        $request->validate(['phone' => 'required|string']);

        $notification = ParentNotification::where('id', $id)
            ->where('guardian_phone', $request->phone)
            ->first();

        if (! $notification) {
            return $this->forbidden();
        }

        if (is_null($notification->read_at)) {
            $notification->update(['read_at' => now()]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * محفظة النقاط: الرصيد + سجل العمليات.
     */
    public function points(Request $request, $id)
    {
        $student = $this->authorizeStudent($request, $id);
        if (! $student) {
            return $this->forbidden();
        }

        $transactions = $student->pointTransactions()->limit(100)->get();

        return response()->json([
            'success' => true,
            'balance' => $student->points,
            'transactions' => $transactions,
        ]);
    }

    /**
     * تسجيل/تحديث FCM token للجهاز (يُستدعى عند فتح التطبيق وعند تجديد التوكن).
     */
    public function registerDevice(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
            'phone' => 'required|string',
            'platform' => 'nullable|string',
        ]);

        $this->saveDevice($request->fcm_token, $request->phone, $request->platform);

        return response()->json(['success' => true]);
    }

    // ===== Helpers =====

    private function saveDevice(string $token, string $phone, ?string $platform): void
    {
        DeviceToken::updateOrCreate(
            ['token' => $token],
            ['guardian_phone' => $phone, 'platform' => $platform]
        );
    }

    /**
     * يتحقق من ملكية الأهل للطالب عبر رقم الهاتف.
     */
    private function authorizeStudent(Request $request, $id): ?Student
    {
        $request->validate(['phone' => 'required|string']);

        return Student::with('shift')
            ->where('id', $id)
            ->where('guardian_phone', $request->phone)
            ->first();
    }

    private function studentPayload(Student $student): array
    {
        return [
            'id' => $student->id,
            'code' => $student->code,
            'name' => $student->name,
            'points' => $student->points,
            'guardian_phone' => $student->guardian_phone,
            'shift' => $student->shift ? [
                'id' => $student->shift->id,
                'name' => $student->shift->name,
            ] : null,
        ];
    }

    private function forbidden()
    {
        return response()->json([
            'success' => false,
            'message' => 'غير مصرّح بالوصول لبيانات هذا الطالب.',
        ], 403);
    }
}
