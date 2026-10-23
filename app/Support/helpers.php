<?php

use App\Support\Settings;

if (! function_exists('money')) {
    /** Format an amount in the base currency, e.g. "Rs 12,500.00". */
    function money($amount, bool $symbol = true): string
    {
        $formatted = number_format((float) $amount, 2);

        return $symbol ? 'Rs ' . $formatted : $formatted;
    }
}

if (! function_exists('qty')) {
    function qty($value): string
    {
        return number_format((int) $value);
    }
}

if (! function_exists('setting')) {
    function setting(string $key, $default = null)
    {
        return Settings::get($key, $default);
    }
}

if (! function_exists('status_badge')) {
    /** Bootstrap soft badge for any document status. */
    function status_badge(?string $status): string
    {
        $map = [
            'draft' => 'secondary', 'open' => 'info', 'ordered' => 'primary', 'costed' => 'info',
            'partial' => 'warning', 'received' => 'success', 'posted' => 'success', 'cancelled' => 'dark',
            'accepted' => 'success', 'rejected' => 'danger', 'converted' => 'primary', 'invoiced' => 'success',
            'unpaid' => 'danger', 'paid' => 'success', 'pending' => 'warning', 'delivered' => 'success',
            'deposited' => 'info', 'cleared' => 'success', 'bounced' => 'danger', 'overdue' => 'danger',
            'cash' => 'success', 'credit' => 'primary', 'active' => 'success', 'inactive' => 'secondary', 'received_cheque' => 'success', 'issued' => 'danger', 'in' => 'success', 'out' => 'danger',
        ];
        $color = $map[$status] ?? 'secondary';
        $label = ucwords(str_replace('_', ' ', (string) $status));

        return '<span class="badge bg-' . $color . '-subtle text-' . $color . ' fw-semibold">' . e($label) . '</span>';
    }
}
