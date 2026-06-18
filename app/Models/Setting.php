<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * إعدادات عامة بنمط مفتاح/قيمة، مع تخزين مؤقت بسيط.
 */
class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public const CACHE_KEY = 'app_settings';

    /** قراءة قيمة إعداد. */
    public static function get(string $key, $default = null)
    {
        $all = Cache::rememberForever(self::CACHE_KEY, function () {
            return static::pluck('value', 'key')->toArray();
        });

        return $all[$key] ?? $default;
    }

    /** كتابة قيمة إعداد. */
    public static function set(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget(self::CACHE_KEY);
    }

    /** كتابة عدّة إعدادات دفعة واحدة. */
    public static function setMany(array $pairs): void
    {
        foreach ($pairs as $key => $value) {
            static::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        Cache::forget(self::CACHE_KEY);
    }
}
