<?php

namespace App\Http\Controllers;

use App\Models\ParentActivity;
use App\Models\DeviceToken;
use App\Models\ParentNotification;
use Illuminate\Http\Request;

/**
 * متابعة النظام: عرض تفاعل تطبيق الأهل مع النظام
 * (مين سجّل دخول، مين فتح التطبيق، مين عرض النقاط/الإشعارات...).
 */
class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        $query = ParentActivity::with('student')->latest();

        // فلترة حسب رقم الهاتف أو اسم الطالب
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('guardian_phone', 'like', "%{$search}%")
                  ->orWhere('student_name', 'like', "%{$search}%");
            });
        }

        // فلترة حسب نوع العملية
        if ($request->filled('action')) {
            $query->where('action', $request->input('action'));
        }

        // فلترة حسب التاريخ
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->input('date'));
        }

        $activities = $query->paginate(40)->withQueryString();

        // ملخص علوي
        $stats = [
            'devices' => DeviceToken::count(),
            'today' => ParentActivity::whereDate('created_at', now()->toDateString())->count(),
            'logins_today' => ParentActivity::where('action', 'login')
                ->whereDate('created_at', now()->toDateString())->count(),
            'notifications' => ParentNotification::count(),
        ];

        $actions = ParentActivity::LABELS;

        return view('monitoring.index', compact('activities', 'stats', 'actions'));
    }
}
