<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $suppliers = Supplier::query()
            ->when($request->q, fn ($q, $s) => $q->where('name', 'like', "%$s%")->orWhere('code', 'like', "%$s%")->orWhere('country', 'like', "%$s%"))
            ->withCount('purchaseOrders')
            ->orderBy('name')->paginate(25)->withQueryString();

        return view('masters.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('masters.suppliers.form', ['supplier' => new Supplier([
            'code' => 'SUP-' . str_pad(Supplier::max('id') + 1, 3, '0', STR_PAD_LEFT),
            'currency' => 'USD', 'payment_terms_days' => 30, 'is_active' => true,
        ])]);
    }

    public function store(Request $request)
    {
        $supplier = Supplier::create($this->validated($request));

        return redirect()->route('suppliers.show', $supplier)->with('success', 'Supplier saved.');
    }

    public function show(Supplier $supplier)
    {
        return view('masters.suppliers.show', [
            'supplier' => $supplier,
            'orders' => $supplier->purchaseOrders()->latest('order_date')->get(),
            'payments' => $supplier->payments()->with('lines')->latest('date')->limit(20)->get(),
            'cheques' => $supplier->cheques()->latest('cheque_date')->limit(10)->get(),
        ]);
    }

    public function edit(Supplier $supplier)
    {
        return view('masters.suppliers.form', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $supplier->update($this->validated($request, $supplier));

        return redirect()->route('suppliers.show', $supplier)->with('success', 'Supplier updated.');
    }

    protected function validated(Request $request, ?Supplier $supplier = null): array
    {
        $data = $request->validate([
            'code' => ['required', 'max:20', Rule::unique('suppliers')->ignore($supplier)],
            'name' => 'required|max:255',
            'country' => 'nullable|max:100',
            'currency' => 'required|size:3',
            'contact_person' => 'nullable|max:255',
            'phone' => 'nullable|max:50',
            'email' => 'nullable|email',
            'address' => 'nullable|max:255',
            'payment_terms_days' => 'required|integer|min:0',
            'opening_balance' => 'nullable|numeric',
        ]);
        $data['currency'] = strtoupper($data['currency']);
        $data['opening_balance'] = $data['opening_balance'] ?? 0;
        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }
}
