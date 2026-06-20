<?php

namespace App\Support;

/**
 * تعريف أقسام لوحة الإدارة وصلاحياتها، وربط مسارات الصفحات بكل قسم.
 */
class AdminPanel
{
    /** الأقسام القابلة للمنح للمشرفين (المفتاح => التسمية). */
    public const SECTIONS = [
        'students' => 'الطلاب (عرض + حضور/غياب)',
        'students_create' => 'إضافة طلاب',
        'students_edit' => 'تعديل طلاب',
        'students_delete' => 'حذف الطلاب',
        'absences' => 'تسجيل الغياب',
        'points' => 'النقاط',
        'shifts' => 'الفترات',
        'teachers' => 'الأساتذة',
        'reports' => 'تقارير الدوام',
        'monitoring' => 'متابعة النظام',
        'announcements' => 'إرسال إعلان',
        'attendance_points' => 'نقاط الحضور',
    ];

    /** الأقسام التي تُقيَّد بالفترات المُسندة للمشرف. */
    public const SHIFT_SCOPED = ['students', 'points', 'reports', 'announcements', 'absences'];

    /** ربط اسم المسار (route name) بالقسم المطلوب صلاحيته. */
    public const ROUTE_PERMISSIONS = [
        // الطلاب — عرض وعمليات يومية
        'students.index' => 'students',
        'students.show' => 'students',
        'students.qr' => 'students',
        'students.checkin' => 'students',
        'students.absent' => 'students',
        // تسجيل الغياب (قسم منفصل)
        'absences.index' => 'absences',
        'absences.store' => 'absences',
        // إضافة/تعديل/حذف — صلاحيات منفصلة
        'students.create' => 'students_create',
        'students.store' => 'students_create',
        'students.edit' => 'students_edit',
        'students.update' => 'students_edit',
        'students.destroy' => 'students_delete',
        // النقاط
        'points.index' => 'points',
        'students.updatePoints' => 'points',
        // الفترات
        'shifts.index' => 'shifts',
        'shifts.store' => 'shifts',
        'shifts.destroy' => 'shifts',
        // الأساتذة
        'teachers.index' => 'teachers',
        'teachers.create' => 'teachers',
        'teachers.store' => 'teachers',
        'teachers.report' => 'teachers',
        'teachers.qr' => 'teachers',
        'teachers.edit' => 'teachers',
        'teachers.update' => 'teachers',
        'teachers.destroy' => 'teachers',
        'teachers.checkin' => 'teachers',
        // تقارير الدوام
        'attendance.byShift' => 'reports',
        'attendance.monthlyReport' => 'reports',
        'attendance.friday' => 'reports',
        // متابعة النظام
        'monitoring.index' => 'monitoring',
        // الإعلانات
        'announcements.create' => 'announcements',
        'announcements.send' => 'announcements',
        // نقاط الحضور (إعدادات)
        'settings.attendanceReward' => 'attendance_points',
        'settings.attendanceReward.update' => 'attendance_points',
        // إدارة المشرفين (للمدير فقط)
        'supervisors.index' => 'supervisors',
        'supervisors.create' => 'supervisors',
        'supervisors.store' => 'supervisors',
        'supervisors.edit' => 'supervisors',
        'supervisors.update' => 'supervisors',
        'supervisors.destroy' => 'supervisors',
    ];

    /** القسم المطلوب لمسار معيّن (أو null إن لم يكن مقيّداً). */
    public static function permissionForRoute(?string $routeName): ?string
    {
        if ($routeName === null) {
            return null;
        }
        return self::ROUTE_PERMISSIONS[$routeName] ?? null;
    }

    /** قائمة الأقسام للعرض في نموذج الصلاحيات. */
    public static function sections(): array
    {
        return self::SECTIONS;
    }
}
