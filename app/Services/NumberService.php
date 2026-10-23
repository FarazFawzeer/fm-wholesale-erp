<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class NumberService
{
    /** Next document number for a prefix, e.g. PO-00001. Must be called inside a transaction. */
    public static function next(string $prefix): string
    {
        DB::table('document_sequences')->insertOrIgnore(['prefix' => $prefix, 'next_number' => 1]);
        $row = DB::table('document_sequences')->where('prefix', $prefix)->lockForUpdate()->first();
        DB::table('document_sequences')->where('prefix', $prefix)->increment('next_number');

        return $prefix . '-' . str_pad($row->next_number, 5, '0', STR_PAD_LEFT);
    }
}
