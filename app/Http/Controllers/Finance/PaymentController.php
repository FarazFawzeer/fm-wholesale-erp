<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Services\PaymentService;
use Illuminate\Http\Request;

/** Customer receipts (in) and supplier payments (out). */
class PaymentController extends Controller
{
    public function index(Request $request, string $direction)
    {
        $payments = Payment::with('lines')->where('direction', $direction)
            ->when($request->q, fn ($q, $s) => $q->where('number', 'like', "%$s%"))
            ->when($request->from, fn ($q, $d) => $q->whereDate('date', '>=', $d))
            ->when($request->to, fn ($q, $d) => $q->whereDate('date', '<=', $d))
            ->latest('date')->latest('id')->paginate(25)->withQueryString();

        return view('finance.payments.index', ['payments' => $payments, 'direction' => $direction]);
    }

    public function create(Request $request, string $direction)
    {
        $isIn = $direction === 'in';
        $partyId = $isIn ? $request->customer_id : $request->supplier_id;
        $party = $partyId ? ($isIn ? Customer::find($partyId) : Supplier::find($partyId)) : null;
        $documents = collect();
        if ($party) {
            $documents = $isIn
                ? Invoice::where('customer_id', $party->id)->open()->orderBy('due_date')->get()
                : PurchaseOrder::where('supplier_id', $party->id)->whereRaw('payable_amount - paid_amount > 0.009')->orderBy('due_date')->get();
        }

        return view('finance.payments.create', [
            'direction' => $direction,
            'party' => $party,
            'parties' => $isIn ? Customer::where('is_active', true)->orderBy('name')->get() : Supplier::where('is_active', true)->orderBy('name')->get(),
            'documents' => $documents,
            'accounts' => Account::where('is_active', true)->get(['id', 'name', 'type']),
            'focusInvoice' => $request->invoice_id,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'direction' => 'required|in:in,out',
            'party_id' => 'required|integer',
            'date' => 'required|date',
            'notes' => 'nullable|string',
            'payments' => 'required|array',
            'payments.*.method' => 'required|in:cash,bank_transfer,card,cheque',
            'payments.*.amount' => 'nullable|numeric|min:0',
            'alloc' => 'nullable|array',
        ]);
        $partyType = $data['direction'] === 'in' ? 'customer' : 'supplier';
        $payment = PaymentService::record($data['direction'], $partyType, (int) $data['party_id'], $data['date'],
            $data['payments'], $data['alloc'] ?? [], $data['notes'] ?? null);

        return redirect()->route('payments.show', $payment)->with('success', ($data['direction'] === 'in' ? 'Receipt ' : 'Payment ') . $payment->number . ' recorded.');
    }

    public function show(Payment $payment)
    {
        $payment->load(['lines.account', 'lines.cheque', 'allocations.invoice', 'allocations.purchaseOrder', 'user']);

        return view('finance.payments.show', ['payment' => $payment, 'party' => $payment->party()]);
    }
}
