<?php

namespace App\Http\Controllers;

use App\Models\Cheque;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Invoice;
use App\Models\Supplier;
use App\Services\PaymentService;
use Illuminate\Http\Request;

/** Dues (who owes / who we owe), cheques and expenses. */
class MoneyController extends Controller
{
    /** The unpaid reminder list: every customer who has not paid, oldest first. No due dates - it stays until paid. */
    public function dues(Request $request)
    {
        $tab = $request->tab === 'suppliers' ? 'suppliers' : 'customers';

        if ($tab === 'customers') {
            $rows = Customer::withDue()->get()->filter(fn ($c) => $c->due > 0.009)->map(function ($c) {
                $oldest = Invoice::where('customer_id', $c->id)->unpaid()->min('date');
                $since = $oldest ? \Carbon\Carbon::parse($oldest) : $c->created_at;

                return ['party' => $c, 'due' => (float) $c->due, 'since' => $since, 'days' => (int) $since->diffInDays(now()->startOfDay()),
                    'bills' => Invoice::where('customer_id', $c->id)->unpaid()->count()];
            })->sortByDesc('days')->values();
        } else {
            $rows = Supplier::withDue()->get()->filter(fn ($s) => $s->due > 0.009)->map(function ($s) {
                $oldest = $s->purchases()->unpaid()->min('date');
                $since = $oldest ? \Carbon\Carbon::parse($oldest) : $s->created_at;

                return ['party' => $s, 'due' => (float) $s->due, 'since' => $since, 'days' => (int) $since->diffInDays(now()->startOfDay()),
                    'bills' => $s->purchases()->unpaid()->count()];
            })->sortByDesc('days')->values();
        }

        return view('money.dues', [
            'tab' => $tab,
            'rows' => $rows,
            'customerTotal' => Customer::withDue()->get()->sum(fn ($c) => max(0, $c->due)),
            'supplierTotal' => Supplier::withDue()->get()->sum(fn ($s) => max(0, $s->due)),
        ]);
    }

    public function cheques(Request $request)
    {
        $status = $request->status ?? 'pending';
        $cheques = Cheque::query()
            ->when($request->direction, fn ($q, $d) => $q->where('direction', $d))
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->orderBy($status === 'pending' ? 'cheque_date' : 'id', $status === 'pending' ? 'asc' : 'desc')
            ->paginate(40)->withQueryString();

        return view('money.cheques', [
            'cheques' => $cheques,
            'status' => $status,
            'pendingIn' => Cheque::where('direction', 'received')->where('status', 'pending')->sum('amount'),
            'pendingOut' => Cheque::where('direction', 'issued')->where('status', 'pending')->sum('amount'),
        ]);
    }

    public function chequeStatus(Request $request, Cheque $cheque)
    {
        $data = $request->validate(['status' => 'required|in:cleared,bounced', 'date' => 'nullable|date']);
        PaymentService::updateCheque($cheque, $data['status'], $data['date'] ?? now()->toDateString());

        return back()->with('success', 'Cheque #' . $cheque->cheque_no . ($data['status'] === 'cleared' ? ' marked as cleared.' : ' marked as returned. The amount is owed again.'));
    }

    public function expenses(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to = $request->to ?? now()->toDateString();
        $query = Expense::with('category', 'user')->whereBetween('date', [$from, $to])
            ->when($request->category, fn ($q, $c) => $q->where('expense_category_id', $c));

        return view('money.expenses', [
            'expenses' => (clone $query)->latest('date')->latest('id')->paginate(40)->withQueryString(),
            'total' => (clone $query)->sum('amount'),
            'byCategory' => (clone $query)->selectRaw('expense_category_id, SUM(amount) AS total')->groupBy('expense_category_id')->with('category')->get()->sortByDesc('total'),
            'categories' => ExpenseCategory::orderBy('name')->get(),
            'from' => $from,
            'to' => $to,
        ]);
    }

    public function storeExpense(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'expense_category_id' => 'nullable|exists:expense_categories,id',
            'new_category' => 'nullable|max:100',
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:cash,card,online,cheque',
            'paid_to' => 'nullable|max:255',
            'note' => 'nullable|max:255',
        ]);
        if (filled($data['new_category'] ?? null)) {
            $data['expense_category_id'] = ExpenseCategory::firstOrCreate(['name' => trim($data['new_category'])])->id;
        }
        if (empty($data['expense_category_id'])) {
            return back()->withInput()->withErrors(['expense_category_id' => 'Choose what the expense is for.']);
        }
        unset($data['new_category']);
        Expense::create($data + ['user_id' => auth()->id()]);

        return back()->with('success', 'Expense of ' . money($data['amount']) . ' saved.');
    }

    public function destroyExpense(Expense $expense)
    {
        abort_unless(auth()->user()->isManager() || $expense->user_id === auth()->id(), 403);
        $expense->delete();

        return back()->with('success', 'Expense deleted.');
    }
}
