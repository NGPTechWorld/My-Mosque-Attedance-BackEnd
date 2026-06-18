<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\Student;
use App\Models\ParentNotification;
use App\Services\FcmService;
use Illuminate\Http\Request;

/**
 * إرسال إعلان/إشعار لأهالي طلاب دوام (أو عدّة دوامات) مختارة.
 * يُحفظ في سجل إشعارات التطبيق ويُرسل عبر Firebase.
 */
class AnnouncementController extends Controller
{
    public function __construct(private FcmService $fcm)
    {
    }

    public function create()
    {
        $query = Shift::withCount('students');

        // المشرف يرى فقط فتراته المُسندة
        $shiftIds = auth()->user()->scopedShiftIds();
        if ($shiftIds !== null) {
            $query->whereIn('id', $shiftIds);
        }

        $shifts = $query->get();
        return view('announcements.create', compact('shifts'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:1000',
            'shift_ids' => 'required|array|min:1',
            'shift_ids.*' => 'integer|exists:shifts,id',
        ], [
            'shift_ids.required' => 'الرجاء اختيار دوام واحد على الأقل.',
        ]);

        // المشرف لا يستطيع الإرسال لفترة غير مُسندة له
        $user = auth()->user();
        foreach ($request->shift_ids as $shiftId) {
            if (! $user->canAccessShift($shiftId)) {
                abort(403, 'ليس لديك صلاحية على إحدى الفترات المختارة.');
            }
        }

        $students = Student::whereIn('shift_id', $request->shift_ids)->get();

        if ($students->isEmpty()) {
            return back()->withInput()->with('error', 'لا يوجد طلاب في الدوام/الدوامات المختارة.');
        }

        $data = ['type' => 'announcement'];
        $now = now();

        // 1) حفظ الإعلان في سجل التطبيق لكل طالب (يظهر داخل تطبيق الأهل)
        $records = $students->map(fn ($student) => [
            'student_id' => $student->id,
            'guardian_phone' => $student->guardian_phone,
            'type' => 'announcement',
            'title' => $request->title,
            'body' => $request->body,
            'data' => json_encode($data, JSON_UNESCAPED_UNICODE),
            'read_at' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ])->all();

        ParentNotification::insert($records);

        // 2) إرسال إشعار Firebase مرة واحدة لكل رقم أهل (تفادي التكرار)
        $phones = $students->pluck('guardian_phone')->filter()->unique();
        foreach ($phones as $phone) {
            $this->fcm->sendToGuardian($phone, $request->title, $request->body, $data);
        }

        return back()->with(
            'success',
            "تم إرسال الإعلان إلى {$phones->count()} ولي أمر ({$students->count()} طالب)."
        );
    }
}
