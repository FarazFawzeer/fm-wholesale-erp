<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerGroup;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = Customer::with('group')
            ->when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w->where('name', 'like', "%$s%")->orWhere('code', 'like', "%$s%")->orWhere('city', 'like', "%$s%")->orWhere('phone', 'like', "%$s%")))
            ->when($request->group, fn ($q, $g) => $q->where('customer_group_id', $g))
            ->orderBy('name')->paginate(25)->withQueryString();

        return view('masters.customers.index', [
            'customers' => $customers,
            'groups' => CustomerGroup::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('masters.customers.form', [
            'customer' => new Customer([
                'code' => 'CUS-' . str_pad(Customer::max('id') + 1, 3, '0', STR_PAD_LEFT),
                'payment_terms_days' => (int) setting('default_payment_terms'),
                'credit_limit' => 500000,
                'is_active' => true,
            ]),
            'groups' => CustomerGroup::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $customer = Customer::create($this->validated($request));

        return redirect()->route('customers.show', $customer)->with('success', 'Customer saved.');
    }

    public function show(Customer $customer)
    {
        $customer->load('group');

        return view('masters.customers.show', [
            'customer' => $customer,
            'balance' => $customer->balance(),
            'overdue' => $customer->overdueAmount(),
            'pendingCheques' => $customer->pendingChequesTotal(),
            'invoices' => $customer->invoices()->latest('date')->latest('id')->limit(20)->get(),
            'payments' => $customer->payments()->with('lines')->latest('date')->limit(10)->get(),
            'cheques' => $customer->cheques()->latest('cheque_date')->limit(10)->get(),
        ]);
    }

    public function edit(Customer $customer)
    {
        return view('masters.customers.form', ['customer' => $customer, 'groups' => CustomerGroup::orderBy('name')->get()]);
    }

    public function update(Request $request, Customer $customer)
    {
        $customer->update($this->validated($request, $customer));

        return redirect()->route('customers.show', $customer)->with('success', 'Customer updated.');
    }

    protected function validated(Request $request, ?Customer $customer = null): array
    {
        $data = $request->validate([
            'code' => ['required', 'max:20', Rule::unique('customers')->ignore($customer)],
            'name' => 'required|max:255',
            'business_name' => 'nullable|max:255',
            'customer_group_id' => 'nullable|exists:customer_groups,id',
            'phone' => 'nullable|max:30',
            'whatsapp' => 'nullable|max:30',
            'email' => 'nullable|email',
            'address' => 'nullable|max:255',
            'city' => 'nullable|max:100',
            'credit_limit' => 'required|numeric|min:0',
            'payment_terms_days' => 'required|integer|min:0',
            'opening_balance' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);
        $data['opening_balance'] = $data['opening_balance'] ?? 0;
        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }
}
