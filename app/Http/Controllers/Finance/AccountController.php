<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\AccountTransaction;
use App\Services\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** Cash, bank and card accounts, their ledgers and the daily cashbook. */
class AccountController extends Controller
{
    public function index()
    {
        return view('finance.accounts.index', [
            'accounts' => Account::orderBy('type')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        Account::create($request->validate([
            'name' => 'required|max:100',
            'type' => 'required|in:cash,bank,card',
            'bank_name' => 'nullable|max:100',
            'account_no' => 'nullable|max:50',
            'opening_balance' => 'required|numeric',
        ]));

        return back()->with('success', 'Account added.');
    }

    /** Move money between accounts, e.g. cash deposited into the bank. */
    public function transfer(Request $request)
    {
        $data = $request->validate([
            'from_account_id' => 'required|exists:accounts,id',
            'to_account_id' => 'required|exists:accounts,id|different:from_account_id',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'description' => 'nullable|max:255',
        ]);
        DB::transaction(function () use ($data) {
            $from = Account::find($data['from_account_id']);
            $to = Account::find($data['to_account_id']);
            $desc = $data['description'] ?: 'Transfer';
            LedgerService::post($from->id, $data['date'], 'transfer', $desc . ' to ' . $to->name, -$data['amount']);
            LedgerService::post($to->id, $data['date'], 'transfer', $desc . ' from ' . $from->name, $data['amount']);
        });

        return back()->with('success', 'Transfer recorded.');
    }

    public function show(Request $request, Account $account)
    {
        $from = $request->input('from', now()->subDays(30)->toDateString());
        $to = $request->input('to', now()->toDateString());
        $opening = $account->opening_balance + (float) $account->transactions()->whereDate('date', '<', $from)->sum('amount');
        $rows = $account->transactions()->whereDate('date', '>=', $from)->whereDate('date', '<=', $to)->orderBy('date')->orderBy('id')->get();

        return view('finance.accounts.show', compact('account', 'from', 'to', 'opening', 'rows'));
    }

    /** Daily cashbook: every money movement on one day across all accounts. */
    public function cashbook(Request $request)
    {
        $date = $request->input('date', now()->toDateString());
        $accounts = Account::where('is_active', true)->orderBy('type')->get()->map(function ($a) use ($date) {
            $opening = $a->opening_balance + (float) $a->transactions()->whereDate('date', '<', $date)->sum('amount');
            $in = (float) $a->transactions()->whereDate('date', $date)->where('amount', '>', 0)->sum('amount');
            $out = (float) $a->transactions()->whereDate('date', $date)->where('amount', '<', 0)->sum('amount');

            return ['account' => $a, 'opening' => $opening, 'in' => $in, 'out' => -$out, 'closing' => $opening + $in + $out];
        });

        return view('finance.accounts.cashbook', [
            'date' => $date,
            'accounts' => $accounts,
            'rows' => AccountTransaction::with('account')->whereDate('date', $date)->orderBy('id')->get(),
        ]);
    }
}
