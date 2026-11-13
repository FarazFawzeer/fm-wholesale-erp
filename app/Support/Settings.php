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
        'item_prefix' => 'ARD',
        'invoice_terms' => 'Please check the goods when you receive them. Thank you!',
    ];

    protected static ?array $cache = null;

    public static function all(): array
    {
        if (static::$cache === null) {
            try {
                static::$cache = array_merge(static::DEFAULTS, Setting::pluck('value', 'key')->all());
            } catch (\Throwable $e) {
                static::$cache = static::DEFAULTS; // database not ready yet
            }
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
