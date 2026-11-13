<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\Supplier;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $direction = $request->direction === 'out' ? 'out' : 'in';
        $query = Payment::with('lines')->where('direction', $direction)
            ->when($request->from, fn ($q, $d) => $q->whereDate('date', '>=', $d))
            ->when($request->to, fn ($q, $d) => $q->whereDate('date', '<=', $d));

        return view('payments.index', [
            'direction' => $direction,
            'payments' => (clone $query)->latest('date')->latest('id')->paginate(30)->withQueryString(),
            'total' => (clone $query)->sum('total'),
        ]);
    }

    /** Get paid by a customer. */
    public function receive(Request $request)
    {
        return $this->form('customer', $request);
    }

    /** Pay a supplier. */
    public function pay(Request $request)
    {
        return $this->form('supplier', $request);
    }

    protected function form(string $partyType, Request $request)
    {
        $isCustomer = $partyType === 'customer';
        $partyId = old('party_id', $isCustomer ? $request->customer : $request->supplier);
        $party = $partyId ? ($isCustomer ? Customer::find($partyId) : Supplier::find($partyId)) : null;

        return view('payments.form', [
            'partyType' => $partyType,
            'parties' => ($isCustomer ? Customer::withDue() : Supplier::withDue())->orderBy('name')->get()->filter(fn ($p) => $p->due > 0.009 || $p->id == $partyId),
            'party' => $party,
            'due' => $party ? max(0, $party->balance()) : 0,
            'openingDue' => $party ? $party->openingDue() : 0,
            'bills' => $party ? ($isCustomer ? $party->unpaidInvoices() : $party->unpaidPurchases()) : collect(),
            'firstDoc' => $isCustomer ? $request->invoice : $request->purchase,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'party_type' => 'required|in:customer,supplier',
            'party_id' => 'required|integer',
            'date' => 'required|date',
            'first_doc' => 'nullable|integer',
            'notes' => 'nullable|max:1000',
            'payments' => 'array',
        ]);
        $lines = PaymentService::cleanLines($request->input('payments', []));
        if (! $lines) {
            throw ValidationException::withMessages(['payments' => 'Enter the amount paid.']);
        }
        $alloc = PaymentService::oldestFirst($data['party_type'], (int) $data['party_id'], PaymentService::linesTotal($lines), $data['first_doc'] ?? null);
        $payment = PaymentService::record($data['party_type'] === 'customer' ? 'in' : 'out', $data['party_type'], (int) $data['party_id'],
            $data['date'], $lines, $alloc, $data['notes'] ?? null);

        return redirect()->route('payments.show', $payment)->with('success', 'Payment of ' . money($payment->total) . ' saved.');
    }

    public function show(Payment $payment)
    {
        $payment->load(['lines.cheque', 'allocations.invoice', 'allocations.purchase', 'user']);
        $party = $payment->party();

        return view('payments.show', ['payment' => $payment, 'party' => $party, 'due' => $party?->balance() ?? 0]);
    }
}
