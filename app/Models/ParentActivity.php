<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * سجل عملية تفاعل من تطبيق الأهل (لقسم متابعة النظام).
 */
class ParentActivity extends Model
{
    protected $fillable = [
        'guardian_phone',
        'student_id',
        'student_name',
        'action',
        'description',
        'platform',
    ];

    /** تسميات عربية لأنواع العمليات. */
    public const LABELS = [
        'login' => 'تسجيل دخول',
        'open_app' => 'فتح التطبيق',
        'view_points' => 'عرض النقاط',
        'view_notifications' => 'عرض الإشعارات',
        'device' => 'تسجيل جهاز',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function getActionLabelAttribute(): string
    {
        return self::LABELS[$this->action] ?? $this->action;
    }

    /**
     * تسجيل عملية تفاعل بسرعة (لا ترمي استثناء حتى لا تعطّل واجهة الـ API).
     */
    public static function log(string $phone, string $action, ?string $description = null, ?int $studentId = null, ?string $studentName = null, ?string $platform = null): void
    {
        try {
            static::create([
                'guardian_phone' => $phone,
                'student_id' => $studentId,
                'student_name' => $studentName,
                'action' => $action,
                'description' => $description,
                'platform' => $platform,
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to log parent activity: ' . $e->getMessage());
        }
    }
}
