<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Services\LedgerService;
use App\Services\NumberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());
        $query = Expense::with(['category', 'account'])->whereDate('date', '>=', $from)->whereDate('date', '<=', $to)
            ->when($request->category_id, fn ($q, $c) => $q->where('expense_category_id', $c));

        return view('finance.expenses.index', [
            'expenses' => (clone $query)->latest('date')->latest('id')->paginate(30)->withQueryString(),
            'byCategory' => (clone $query)->selectRaw('expense_category_id, SUM(amount) total')->groupBy('expense_category_id')->with('category')->get()->sortByDesc('total'),
            'total' => (clone $query)->sum('amount'),
            'categories' => ExpenseCategory::orderBy('name')->get(),
            'from' => $from,
            'to' => $to,
        ]);
    }

    public function create()
    {
        return view('finance.expenses.create', [
            'categories' => ExpenseCategory::orderBy('name')->get(),
            'accounts' => Account::where('is_active', true)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'payee' => 'nullable|max:255',
            'description' => 'nullable|max:255',
        ]);
        DB::transaction(function () use ($data) {
            $e = Expense::create($data + ['number' => NumberService::next('EXP'), 'user_id' => auth()->id()]);
            LedgerService::post($e->account_id, $e->date, 'expense', $e->category->name . ': ' . ($e->description ?: $e->payee), -$e->amount, $e);
        });

        return redirect()->route('expenses.index')->with('success', 'Expense recorded.');
    }
}
