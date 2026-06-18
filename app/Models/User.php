<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'username', // أضف هذا
        'password',
        'role',         // admin | supervisor
        'permissions',  // الأقسام المسموحة (للمشرف)
        'shift_ids',    // الفترات التي يديرها المشرف
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'permissions' => 'array',
            'shift_ids' => 'array',
        ];
    }

    /** هل المستخدم مدير عام (وصول كامل)؟ */
    public function isAdmin(): bool
    {
        return ($this->role ?? 'admin') === 'admin';
    }

    /** هل يملك صلاحية الوصول لقسم معيّن؟ */
    public function hasSection(string $key): bool
    {
        if ($this->isAdmin()) {
            return true;
        }
        return in_array($key, $this->permissions ?? [], true);
    }

    /**
     * الفترات التي يستطيع الوصول إليها:
     * null = كل الفترات (للمدير)، أو مصفوفة معرّفات (للمشرف).
     */
    public function scopedShiftIds(): ?array
    {
        if ($this->isAdmin()) {
            return null;
        }
        return array_map('intval', $this->shift_ids ?? []);
    }

    /** هل يستطيع الوصول لفترة محددة؟ */
    public function canAccessShift($id): bool
    {
        $ids = $this->scopedShiftIds();
        return $ids === null || in_array((int) $id, $ids, true);
    }
}
