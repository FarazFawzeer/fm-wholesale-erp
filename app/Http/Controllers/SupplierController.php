<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $suppliers = Supplier::withDue()
            ->when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w->where('name', 'like', "%$s%")->orWhere('phone', 'like', "%$s%")->orWhere('contact_person', 'like', "%$s%")))
            ->when($request->show === 'owe', fn ($q) => $q->having('due', '>', 0.009))
            ->orderBy('name')
            ->paginate(30)->withQueryString();

        return view('suppliers.index', [
            'suppliers' => $suppliers,
            'totalDue' => Supplier::withDue()->get()->sum(fn ($s) => max(0, $s->due)),
        ]);
    }

    public function create()
    {
        return view('suppliers.form', ['supplier' => new Supplier(['is_active' => true])]);
    }

    public function store(Request $request)
    {
        $supplier = Supplier::create($this->validated($request));

        return redirect()->route('suppliers.show', $supplier)->with('success', 'Supplier saved.');
    }

    public function quickStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|max:255',
            'phone' => 'nullable|max:30',
            'contact_person' => 'nullable|max:255',
        ]);
        $supplier = Supplier::create($data + ['is_active' => true]);

        return response()->json(['id' => $supplier->id, 'label' => $supplier->label(), 'data' => ['due' => 0]]);
    }

    public function show(Supplier $supplier)
    {
        return view('suppliers.show', [
            'supplier' => $supplier,
            'due' => $supplier->balance(),
            'openingDue' => $supplier->openingDue(),
            'unpaid' => $supplier->unpaidPurchases(),
            'purchases' => $supplier->purchases()->latest('date')->latest('id')->limit(20)->get(),
            'payments' => Payment::with('lines')->where('party_type', 'supplier')->where('party_id', $supplier->id)->latest('date')->latest('id')->limit(20)->get(),
        ]);
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.form', ['supplier' => $supplier]);
    }

    public function update(Request $request, Supplier $supplier)
    {
        $supplier->update($this->validated($request));

        return redirect()->route('suppliers.show', $supplier)->with('success', 'Supplier updated.');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->purchases()->exists() || $supplier->payments()->exists()) {
            $supplier->update(['is_active' => false]);

            return redirect()->route('suppliers.index')->with('success', 'This supplier has bills, so it was hidden instead of deleted.');
        }
        $supplier->delete();

        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted.');
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => 'required|max:255',
            'contact_person' => 'nullable|max:255',
            'phone' => 'nullable|max:30',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|max:255',
            'opening_balance' => 'nullable|numeric|min:0',
            'notes' => 'nullable|max:2000',
        ]);
        $data['opening_balance'] = (float) ($data['opening_balance'] ?? 0);
        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }
}
