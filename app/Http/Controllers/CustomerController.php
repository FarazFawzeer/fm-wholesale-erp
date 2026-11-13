<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Payment;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = Customer::withDue()
            ->when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w->where('name', 'like', "%$s%")->orWhere('shop_name', 'like', "%$s%")
                ->orWhere('city', 'like', "%$s%")->orWhere('phone', 'like', "%$s%")))
            ->when($request->show === 'owes', fn ($q) => $q->having('due', '>', 0.009))
            ->orderBy($request->show === 'owes' ? 'due' : 'name', $request->show === 'owes' ? 'desc' : 'asc')
            ->paginate(30)->withQueryString();

        return view('customers.index', [
            'customers' => $customers,
            'totalDue' => Customer::withDue()->get()->sum(fn ($c) => max(0, $c->due)),
        ]);
    }

    public function create()
    {
        return view('customers.form', ['customer' => new Customer(['is_active' => true])]);
    }

    public function store(Request $request)
    {
        $customer = Customer::create($this->validated($request));

        return redirect()->route('customers.show', $customer)->with('success', 'Customer saved.');
    }

    /** Quick add from the sale / payment screens. */
    public function quickStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|max:255',
            'shop_name' => 'nullable|max:255',
            'phone' => 'nullable|max:30',
            'city' => 'nullable|max:100',
        ]);
        $customer = Customer::create($data + ['whatsapp' => $data['phone'] ?? null, 'is_active' => true]);

        return response()->json(['id' => $customer->id, 'label' => $customer->label(), 'data' => ['due' => 0]]);
    }

    public function show(Customer $customer)
    {
        $payments = Payment::with('lines')->where('party_type', 'customer')->where('party_id', $customer->id)->latest('date')->latest('id')->limit(20)->get();

        return view('customers.show', [
            'customer' => $customer,
            'due' => $customer->balance(),
            'openingDue' => $customer->openingDue(),
            'unpaid' => $customer->unpaidInvoices(),
            'invoices' => $customer->invoices()->latest('date')->latest('id')->limit(20)->get(),
            'payments' => $payments,
            'pendingCheques' => $customer->cheques()->where('status', 'pending')->orderBy('cheque_date')->get(),
            'totalBought' => (float) $customer->invoices()->sum('total') - (float) $customer->invoices()->sum('returned_amount'),
        ]);
    }

    public function edit(Customer $customer)
    {
        return view('customers.form', ['customer' => $customer]);
    }

    public function update(Request $request, Customer $customer)
    {
        $customer->update($this->validated($request));

        return redirect()->route('customers.show', $customer)->with('success', 'Customer updated.');
    }

    public function destroy(Customer $customer)
    {
        if ($customer->invoices()->exists() || $customer->payments()->exists()) {
            $customer->update(['is_active' => false]);

            return redirect()->route('customers.index')->with('success', 'This customer has bills, so it was hidden instead of deleted.');
        }
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer deleted.');
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => 'required|max:255',
            'shop_name' => 'nullable|max:255',
            'phone' => 'nullable|max:30',
            'whatsapp' => 'nullable|max:30',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|max:255',
            'city' => 'nullable|max:100',
            'opening_balance' => 'nullable|numeric|min:0',
            'notes' => 'nullable|max:2000',
        ]);
        $data['opening_balance'] = (float) ($data['opening_balance'] ?? 0);
        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }
}
