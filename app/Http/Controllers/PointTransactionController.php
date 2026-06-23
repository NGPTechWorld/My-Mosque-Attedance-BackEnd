<?php

namespace App\Http\Controllers;

use App\Models\PointTransaction;
use Illuminate\Http\Request;

/**
 * سجل تحويلات النقاط (عرض فقط) في لوحة الإدارة.
 */
class PointTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = PointTransaction::with(['student', 'teacher', 'pointReason'])
            ->latest();

        // فلترة اختيارية بالنوع (إضافة/حذف)
        if (in_array($request->type, ['add', 'remove'], true)) {
            $query->where('type', $request->type);
        }

        // بحث باسم الطالب
        if ($request->filled('q')) {
            $q = $request->q;
            $query->whereHas('student', fn ($s) => $s->where('name', 'like', "%{$q}%"));
        }

        $transactions = $query->paginate(50)->withQueryString();

        return view('point_transactions.index', compact('transactions'));
    }
}
