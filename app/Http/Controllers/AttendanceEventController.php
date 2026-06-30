<?php

namespace App\Http\Controllers;

use App\Models\AttendanceEvent;
use App\Models\EventAttendance;
use App\Models\Shift;
use App\Models\Student;
use Illuminate\Http\Request;

/**
 * إدارة مناسبات الحضور الخاصة (CRUD) + تقرير الحضور.
 */
class AttendanceEventController extends Controller
{
    public function index()
    {
        $events = AttendanceEvent::with('shifts')->orderBy('name')->get();
        return view('attendance_events.index', compact('events'));
    }

    public function create()
    {
        $shifts = Shift::all();
        return view('attendance_events.create', compact('shifts'));
    }

    public function store(Request $request)
    {
        $event = AttendanceEvent::create($this->validateData($request));
        $event->shifts()->sync($request->shift_ids);

        return redirect()->route('attendance_events.index')->with('success', 'تم إضافة المناسبة بنجاح');
    }

    public function edit($id)
    {
        $event = AttendanceEvent::with('shifts')->findOrFail($id);
        $shifts = Shift::all();
        return view('attendance_events.edit', compact('event', 'shifts'));
    }

    public function update(Request $request, $id)
    {
        $event = AttendanceEvent::findOrFail($id);
        $event->update($this->validateData($request));
        $event->shifts()->sync($request->shift_ids);

        return redirect()->route('attendance_events.index')->with('success', 'تم تعديل المناسبة');
    }

    public function destroy($id)
    {
        AttendanceEvent::findOrFail($id)->delete();
        return redirect()->route('attendance_events.index')->with('success', 'تم حذف المناسبة');
    }

    /**
     * تقرير حضور مناسبة محددة بتاريخ محدد (ضمن تقارير الدوام).
     */
    public function report(Request $request)
    {
        $events = AttendanceEvent::orderBy('name')->get();
        $eventId = $request->input('event_id');
        $date = $request->input('date', now()->toDateString());
        $shiftIds = auth()->user()->scopedShiftIds();

        $event = null;
        $rows = collect();

        if ($eventId) {
            $event = AttendanceEvent::with('shifts')->find($eventId);
            if ($event) {
                $allowedShiftIds = $event->shifts->pluck('id');
                if ($shiftIds !== null) {
                    $allowedShiftIds = $allowedShiftIds->intersect($shiftIds);
                }

                $students = Student::with('shift')
                    ->whereIn('shift_id', $allowedShiftIds)
                    ->orderBy('name')
                    ->get();

                $attended = EventAttendance::where('attendance_event_id', $event->id)
                    ->where('date', $date)
                    ->get()
                    ->keyBy('student_id');

                $rows = $students->map(fn ($s) => [
                    'student' => $s,
                    'present' => $attended->has($s->id),
                    'time' => $attended[$s->id]->check_in_time ?? null,
                ]);
            }
        }

        return view('attendance_events.report', compact('events', 'event', 'date', 'rows'));
    }

    private function validateData(Request $request): array
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'days' => 'required|array|min:1',
            'days.*' => 'integer|between:0,6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'points' => 'required|integer|min:0|max:100000',
            'message' => 'nullable|string|max:255',
            'active' => 'nullable|boolean',
            'shift_ids' => 'required|array|min:1',
            'shift_ids.*' => 'exists:shifts,id',
        ]);

        $validated['days'] = array_map('intval', $request->input('days', []));
        $validated['active'] = $request->boolean('active');
        unset($validated['shift_ids']);

        return $validated;
    }
}
