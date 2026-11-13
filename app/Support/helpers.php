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
        $labels = ['costed' => 'On the way', 'partial' => 'Part paid', 'posted' => 'Done', 'open' => 'Open', 'converted' => 'Moved to pre-order',
            'invoiced' => 'Invoiced', 'deposited' => 'In bank', 'bounced' => 'Returned', 'credit' => 'Credit', 'cash' => 'Cash'];
        $label = $labels[$status] ?? ucwords(str_replace('_', ' ', (string) $status));

        return '<span class="badge bg-' . $color . '-subtle text-' . $color . ' fw-semibold">' . e($label) . '</span>';
    }
}

if (! function_exists('amount_in_words')) {
    /** 258400.50 => "Two Hundred Fifty Eight Thousand Four Hundred Rupees and Fifty Cents Only" */
    function amount_in_words(float $amount): string
    {
        $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve',
            'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
        $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
        $below1000 = function (int $n) use ($ones, $tens): string {
            $w = [];
            if ($n >= 100) { $w[] = $ones[intdiv($n, 100)] . ' Hundred'; $n %= 100; }
            if ($n >= 20) { $w[] = $tens[intdiv($n, 10)] . ($n % 10 ? ' ' . $ones[$n % 10] : ''); }
            elseif ($n > 0) { $w[] = $ones[$n]; }
            return implode(' ', $w);
        };
        $amount = round(abs($amount), 2);
        $rupees = (int) floor($amount);
        $cents = (int) round(($amount - $rupees) * 100);
        $parts = [];
        foreach ([1000000000 => 'Billion', 1000000 => 'Million', 1000 => 'Thousand', 1 => ''] as $size => $name) {
            if ($rupees >= $size) {
                $parts[] = trim($below1000(intdiv($rupees, $size)) . ' ' . $name);
                $rupees %= $size;
            }
        }
        $text = ($parts ? implode(' ', $parts) : 'Zero') . ' Rupees';
        if ($cents) $text .= ' and ' . $below1000($cents) . ' Cents';
        return $text . ' Only';
    }
}
