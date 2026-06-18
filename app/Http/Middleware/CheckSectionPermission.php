<?php

namespace App\Http\Middleware;

use App\Support\AdminPanel;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * يمنع المشرف من الوصول لأي قسم لم تُمنح له صلاحيته.
 * يستنتج القسم المطلوب من اسم المسار الحالي تلقائياً.
 */
class CheckSectionPermission
{
    public function handle(Request $request, Closure $next): Response
    {
        $permission = AdminPanel::permissionForRoute($request->route()?->getName());

        // مسار غير مقيّد (مثل الرئيسية) → مسموح
        if ($permission === null) {
            return $next($request);
        }

        $user = $request->user();

        // المدير يصل لكل شيء
        if ($user && $user->isAdmin()) {
            return $next($request);
        }

        // إدارة المشرفين للمدير فقط
        if ($permission === 'supervisors') {
            abort(403, 'هذا القسم مخصّص للمدير العام فقط.');
        }

        if ($user && $user->hasSection($permission)) {
            return $next($request);
        }

        abort(403, 'ليس لديك صلاحية الوصول لهذا القسم.');
    }
}
