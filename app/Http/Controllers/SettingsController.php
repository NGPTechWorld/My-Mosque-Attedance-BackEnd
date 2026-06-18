<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\AttendanceRewardService;
use Illuminate\Http\Request;

/**
 * إعدادات لوحة الإدارة — حالياً: إعدادات نقاط الحضور التلقائية.
 */
class SettingsController extends Controller
{
    public function attendanceReward()
    {
        $reward = [
            'enabled' => Setting::get(AttendanceRewardService::KEY_ENABLED, '0') === '1',
            'points' => (int) Setting::get(AttendanceRewardService::KEY_POINTS, 0),
            'message' => Setting::get(AttendanceRewardService::KEY_MESSAGE, 'تم تسجيل الحضور'),
        ];

        return view('settings.attendance_reward', compact('reward'));
    }

    public function updateAttendanceReward(Request $request)
    {
        $request->validate([
            'points' => 'required|integer|min:0|max:100000',
            'message' => 'nullable|string|max:255',
        ]);

        Setting::setMany([
            AttendanceRewardService::KEY_ENABLED => $request->boolean('enabled') ? '1' : '0',
            AttendanceRewardService::KEY_POINTS => (string) $request->input('points'),
            AttendanceRewardService::KEY_MESSAGE => $request->input('message'),
        ]);

        return back()->with('success', 'تم حفظ إعدادات نقاط الحضور بنجاح');
    }
}
