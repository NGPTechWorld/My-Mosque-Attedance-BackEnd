<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\TeacherAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    // ===== لوحة الويب =====

    public function index()
    {
        $today = now()->toDateString();
        $teachers = Teacher::with(['attendances' => function ($q) use ($today) {
            $q->where('date', $today);
        }])->get();

        return view('teachers.index', compact('teachers', 'today'));
    }

    public function create()
    {
        return view('teachers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'nullable|string|unique:teachers,code',
            'name' => 'required|string',
            'phone' => 'nullable|string',
            'subject' => 'nullable|string',
        ]);

        Teacher::create($request->only(['code', 'name', 'phone', 'subject']));

        return redirect()->route('teachers.index')->with('success', 'تم إضافة الأستاذ بنجاح');
    }

    public function edit($id)
    {
        $teacher = Teacher::findOrFail($id);
        return view('teachers.edit', compact('teacher'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'code' => 'nullable|string|unique:teachers,code,' . $id,
            'name' => 'required|string',
            'phone' => 'nullable|string',
            'subject' => 'nullable|string',
        ]);

        $teacher = Teacher::findOrFail($id);
        $teacher->update($request->only(['code', 'name', 'phone', 'subject']));

        return redirect()->route('teachers.index')->with('success', 'تم تعديل بيانات الأستاذ');
    }

    public function destroy($id)
    {
        Teacher::findOrFail($id)->delete();
        return redirect()->route('teachers.index')->with('success', 'تم حذف الأستاذ');
    }

    // تسجيل حضور أستاذ من لوحة الويب
    public function checkInWeb($id)
    {
        $teacher = Teacher::findOrFail($id);
        $today = now();

        $already = TeacherAttendance::where('teacher_id', $teacher->id)
            ->where('date', $today->toDateString())
            ->exists();

        if ($already) {
            return back()->with('error', 'تم تسجيل حضور الأستاذ مسبقاً اليوم');
        }

        TeacherAttendance::create([
            'teacher_id' => $teacher->id,
            'date' => $today->toDateString(),
            'check_in_time' => $today->format('H:i:s'),
        ]);

        return back()->with('success', 'تم تسجيل حضور الأستاذ بنجاح');
    }

    // صفحة طباعة الـ QR
    public function qr($id)
    {
        $teacher = Teacher::findOrFail($id);
        return view('teachers.qr', compact('teacher'));
    }

    // تقرير حضور الأساتذة بين تاريخين
    public function report(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());

        $teachers = Teacher::with(['attendances' => function ($q) use ($from, $to) {
            $q->whereBetween('date', [$from, $to])->orderBy('date');
        }])->get();

        return view('teachers.report', compact('teachers', 'from', 'to'));
    }

    // ===== API (لتطبيق الإدارة عبر الـ QR) =====

    public function checkInApi(Request $request)
    {
        $request->validate(['teacher_id' => 'required']);

        // القيمة الممسوحة قد تكون الكود أو الـ id
        $teacher = Teacher::resolveByCodeOrId($request->teacher_id);
        if (! $teacher) {
            return response()->json(['message' => 'الأستاذ غير موجود.'], 404);
        }

        $today = Carbon::now();

        $already = TeacherAttendance::where('teacher_id', $teacher->id)
            ->where('date', $today->toDateString())
            ->exists();

        if ($already) {
            return response()->json(['message' => 'تم تسجيل حضور الأستاذ مسبقاً اليوم.'], 400);
        }

        $attendance = TeacherAttendance::create([
            'teacher_id' => $teacher->id,
            'date' => $today->toDateString(),
            'check_in_time' => $today->format('H:i:s'),
        ]);

        return response()->json([
            'message' => 'تم تسجيل حضور الأستاذ بنجاح.',
            'teacher' => ['id' => $teacher->id, 'name' => $teacher->name],
            'attendance' => $attendance,
        ]);
    }

    public function index_api()
    {
        return Teacher::all();
    }
}
