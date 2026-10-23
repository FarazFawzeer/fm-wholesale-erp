<?php

namespace App\Support;

use App\Models\Setting;

class Settings
{
    public const DEFAULTS = [
        'company_name' => 'FM Shoes',
        'company_tagline' => 'Wholesale in Imported Shoes & Footwear Items',
        'company_address' => 'Colombo, Sri Lanka',
        'company_phone' => '',
        'currency' => 'LKR',
        'default_markup_percent' => 35,
        'min_margin_percent' => 10,
        'max_discount_percent' => 10,
        'default_payment_terms' => 30,
        'reminder_days_before_due' => 3,
    ];

    protected static ?array $cache = null;

    public static function all(): array
    {
        if (static::$cache === null) {
            static::$cache = array_merge(static::DEFAULTS, Setting::pluck('value', 'key')->all());
        }

        return static::$cache;
    }

    public static function get(string $key, $default = null)
    {
        return static::all()[$key] ?? $default;
    }

    public static function set(array $values): void
    {
        foreach ($values as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        static::$cache = null;
    }
}
