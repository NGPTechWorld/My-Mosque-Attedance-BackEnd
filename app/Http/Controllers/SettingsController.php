<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Shift;
use App\Services\AttendanceRewardService as R;
use Illuminate\Http\Request;

/**
 * إعدادات النقاط التلقائية: نقاط الحضور + خصم التأخير + خصم الغياب،
 * مع تحديد وقت التأخير لكل فترة.
 */
class SettingsController extends Controller
{
    public function attendanceReward()
    {
        $reward = [
            'enabled' => Setting::get(R::KEY_ENABLED, '0') === '1',
            'points' => (int) Setting::get(R::KEY_POINTS, 0),
            'message' => Setting::get(R::KEY_MESSAGE, 'مكافأة الحضور'),
        ];

        $late = [
            'enabled' => Setting::get(R::KEY_LATE_ENABLED, '0') === '1',
            'points' => (int) Setting::get(R::KEY_LATE_POINTS, 0),
            'message' => Setting::get(R::KEY_LATE_MESSAGE, 'خصم تأخير'),
        ];

        $absence = [
            'enabled' => Setting::get(R::KEY_ABSENCE_ENABLED, '0') === '1',
            'points' => (int) Setting::get(R::KEY_ABSENCE_POINTS, 0),
            'message' => Setting::get(R::KEY_ABSENCE_MESSAGE, 'خصم غياب'),
        ];

        $friday = [
            'enabled' => Setting::get(R::KEY_FRIDAY_ENABLED, '0') === '1',
            'points' => (int) Setting::get(R::KEY_FRIDAY_POINTS, 0),
            'message' => Setting::get(R::KEY_FRIDAY_MESSAGE, 'نقاط حضور الجمعة'),
        ];

        $shifts = Shift::all();

        return view('settings.attendance_reward', compact('reward', 'late', 'absence', 'friday', 'shifts'));
    }

    public function updateAttendanceReward(Request $request)
    {
        $request->validate([
            'points' => 'required|integer|min:0|max:100000',
            'message' => 'nullable|string|max:255',
            'late_points' => 'required|integer|min:0|max:100000',
            'late_message' => 'nullable|string|max:255',
            'absence_points' => 'required|integer|min:0|max:100000',
            'absence_message' => 'nullable|string|max:255',
            'friday_points' => 'required|integer|min:0|max:100000',
            'friday_message' => 'nullable|string|max:255',
            'late_times' => 'nullable|array',
            'late_times.*' => 'nullable|date_format:H:i',
        ]);

        Setting::setMany([
            R::KEY_ENABLED => $request->boolean('enabled') ? '1' : '0',
            R::KEY_POINTS => (string) $request->input('points'),
            R::KEY_MESSAGE => $request->input('message'),

            R::KEY_LATE_ENABLED => $request->boolean('late_enabled') ? '1' : '0',
            R::KEY_LATE_POINTS => (string) $request->input('late_points'),
            R::KEY_LATE_MESSAGE => $request->input('late_message'),

            R::KEY_ABSENCE_ENABLED => $request->boolean('absence_enabled') ? '1' : '0',
            R::KEY_ABSENCE_POINTS => (string) $request->input('absence_points'),
            R::KEY_ABSENCE_MESSAGE => $request->input('absence_message'),

            R::KEY_FRIDAY_ENABLED => $request->boolean('friday_enabled') ? '1' : '0',
            R::KEY_FRIDAY_POINTS => (string) $request->input('friday_points'),
            R::KEY_FRIDAY_MESSAGE => $request->input('friday_message'),
        ]);

        // وقت التأخير لكل فترة
        foreach ($request->input('late_times', []) as $shiftId => $time) {
            $shift = Shift::find($shiftId);
            if ($shift) {
                $shift->late_time = $time ?: null;
                $shift->save();
            }
        }

        return back()->with('success', 'تم حفظ إعدادات النقاط بنجاح');
    }
}
