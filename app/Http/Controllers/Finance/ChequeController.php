<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Cheque;
use App\Services\PaymentService;
use Illuminate\Http\Request;

/** Received (incl. post-dated) and issued cheques with due dates and clearance status. */
class ChequeController extends Controller
{
    public function index(Request $request)
    {
        $direction = $request->input('direction', 'received');
        $base = Cheque::where('direction', $direction);

        $cheques = (clone $base)
            ->when($request->status === 'open', fn ($q) => $q->whereIn('status', ['pending', 'deposited']))
            ->when($request->status && $request->status !== 'open', fn ($q) => $q->where('status', $request->status))
            ->when($request->due === 'week', fn ($q) => $q->whereDate('cheque_date', '<=', now()->addDays(7)))
            ->orderByRaw("FIELD(status, 'pending', 'deposited', 'bounced', 'cleared', 'cancelled')")
            ->orderBy('cheque_date')->paginate(30)->withQueryString();

        return view('finance.cheques.index', [
            'cheques' => $cheques,
            'direction' => $direction,
            'accounts' => Account::where('type', 'bank')->get(),
            'summary' => [
                'pending' => (clone $base)->whereIn('status', ['pending', 'deposited'])->sum('amount'),
                'pdc' => (clone $base)->whereIn('status', ['pending'])->whereDate('cheque_date', '>', now())->sum('amount'),
                'due' => (clone $base)->whereIn('status', ['pending', 'deposited'])->whereDate('cheque_date', '<=', now())->sum('amount'),
                'bounced' => (clone $base)->where('status', 'bounced')->sum('amount'),
            ],
        ]);
    }

    public function status(Request $request, Cheque $cheque)
    {
        $data = $request->validate([
            'status' => 'required|in:deposited,cleared,bounced,cancelled',
            'date' => 'required|date',
            'account_id' => 'nullable|exists:accounts,id',
            'remarks' => 'nullable|string|max:255',
        ]);
        PaymentService::updateChequeStatus($cheque, $data['status'], $data['date'], $data['account_id'] ?? null, $data['remarks'] ?? null);

        $msg = match ($data['status']) {
            'cleared' => 'Cheque cleared and bank balance updated.',
            'bounced' => 'Cheque marked as returned. The amount is back on the ' . $cheque->party_type . "'s balance.",
            default => 'Cheque marked as ' . $data['status'] . '.',
        };

        return back()->with('success', $msg);
    }
}
