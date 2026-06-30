<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointReason extends Model
{
    protected $fillable = [
        'name',
        'type',     // add | remove
        'amount',   // الكمية (موجبة دائماً)
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'amount' => 'integer',
    ];

    // الفترات التي يتاح فيها هذا السبب
    public function shifts()
    {
        return $this->belongsToMany(Shift::class, 'point_reason_shift')->withTimestamps();
    }

    /** هل هذا السبب متاح لهذه الفترة؟ (سبب بلا فترات = متاح للجميع كحالة أمان) */
    public function availableForShift($shiftId): bool
    {
        if ($this->shifts->isEmpty()) {
            return true;
        }
        return $shiftId !== null && $this->shifts->contains('id', $shiftId);
    }

    /** الكمية بإشارتها: موجبة للإضافة، سالبة للحذف. */
    public function signedAmount(): int
    {
        return $this->type === 'remove' ? -$this->amount : $this->amount;
    }

    /** نطاق الأسباب المفعّلة فقط. */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
