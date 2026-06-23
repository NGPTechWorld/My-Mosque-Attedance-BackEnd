<?php

namespace App\Http\Controllers;

use App\Models\PointReason;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;

/**
 * واجهات API الخاصة بتطبيق الأساتذة (إضافة/حذف النقاط عبر مسح الباركود).
 * تتبع نموذج الهوية بالطلب بدون توكن، اتساقاً مع تطبيق الأهل.
 */
class TeacherPointController extends Controller
{
    /** تسجيل دخول الأستاذ بالاسم ورقم التواصل (مطابقة لأستاذ موجود مسبقاً). */
    public function login(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string',
        ]);

        $teacher = Teacher::with('shifts')
            ->whereRaw('TRIM(name) = ?', [trim($request->name)])
            ->whereRaw('TRIM(phone) = ?', [trim($request->phone)])
            ->first();

        if (! $teacher) {
            return response()->json([
                'message' => 'لم يتم العثور على الأستاذ. تواصل مع الإدارة.',
            ], 404);
        }

        return response()->json([
            'teacher' => $this->teacherPayload($teacher),
        ]);
    }

    /** قائمة الأسباب المفعّلة (عامة لكل الفترات). */
    public function reasons()
    {
        $reasons = PointReason::active()
            ->orderBy('name')
            ->get(['id', 'name', 'type', 'amount']);

        return response()->json(['reasons' => $reasons]);
    }

    /** تطبيق النقاط على طالب بعد مسح باركوده. */
    public function applyPoints(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required',
            'student_code' => 'required',
            'point_reason_id' => 'required',
            'note' => 'nullable|string|max:255',
        ]);

        $teacher = Teacher::with('shifts')->find($request->teacher_id);
        if (! $teacher) {
            return response()->json(['message' => 'الأستاذ غير موجود.'], 404);
        }

        $student = Student::resolveByCodeOrId($request->student_code);
        if (! $student) {
            return response()->json(['message' => 'الطالب غير موجود.'], 404);
        }

        // التحقق: الطالب يجب أن يكون من إحدى فترات الأستاذ
        if (! $teacher->teachesShift($student->shift_id)) {
            return response()->json(['message' => 'الطالب ليس من فترتك.'], 422);
        }

        $reason = PointReason::active()->find($request->point_reason_id);
        if (! $reason) {
            return response()->json(['message' => 'السبب غير متاح. أعد تحميل القائمة.'], 404);
        }

        $signed = $reason->signedAmount();
        $note = $request->note ?: null;

        // نلحق ملاحظة الأستاذ بنص السبب حتى تظهر مباشرة في محفظة الأهل
        $reasonText = $note ? ($reason->name . ' - ' . $note) : $reason->name;

        $transaction = $student->addPoints($signed, $reasonText, [
            'teacher_id' => $teacher->id,
            'point_reason_id' => $reason->id,
            'note' => $note,
        ]);

        return response()->json([
            'message' => 'تمت العملية بنجاح.',
            'student_name' => $student->name,
            'reason_name' => $reason->name,
            'applied' => $signed,
            'new_balance' => $student->points,
            'transaction_id' => $transaction->id,
        ]);
    }

    private function teacherPayload(Teacher $teacher): array
    {
        return [
            'id' => $teacher->id,
            'name' => $teacher->name,
            'phone' => $teacher->phone,
            'shifts' => $teacher->shifts->map(fn ($s) => [
                'id' => $s->id,
                'name' => $s->name,
            ])->values(),
        ];
    }
}
