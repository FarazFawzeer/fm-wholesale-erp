<?php

namespace App\Services;

use App\Models\AccountTransaction;
use Illuminate\Database\Eloquent\Model;

class LedgerService
{
    /** Record money in (+) or out (-) of a cash / bank / card account. */
    public static function post(int $accountId, $date, string $type, string $description, float $amount, ?Model $reference = null): void
    {
        if (round($amount, 2) == 0.0) {
            return;
        }

        AccountTransaction::create([
            'account_id' => $accountId,
            'date' => $date,
            'type' => $type,
            'description' => $description,
            'amount' => $amount,
            'reference_type' => $reference ? $reference->getMorphClass() : null,
            'reference_id' => $reference?->getKey(),
        ]);
    }
}
