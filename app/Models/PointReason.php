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
