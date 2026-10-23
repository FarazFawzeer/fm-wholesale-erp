<?php

namespace App\Http\Controllers\Purchasing;

use App\Http\Controllers\Controller;
use App\Models\Grn;
use App\Models\PurchaseOrder;
use App\Services\PurchaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GrnController extends Controller
{
    public function index(Request $request)
    {
        $grns = Grn::with(['supplier', 'purchaseOrder', 'warehouse'])
            ->withSum('items as received', 'received_qty')->withSum('items as damaged', 'damaged_qty')
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest('received_date')->latest('id')->paginate(20)->withQueryString();

        return view('purchasing.grns.index', compact('grns'));
    }

    /** Start a GRN from a PO: all outstanding quantities pre-filled. */
    public function store(Request $request, PurchaseOrder $purchaseOrder)
    {
        abort_unless($purchaseOrder->canReceive(), 403, 'This PO is not ready to receive.');
        $data = $request->validate(['warehouse_id' => 'required|exists:warehouses,id', 'received_date' => 'required|date']);
        if ($draft = $purchaseOrder->grns()->where('status', 'draft')->first()) {
            return redirect()->route('grns.edit', $draft)->with('success', 'Continue the draft GRN for this order.');
        }
        $grn = PurchaseService::draftGrn($purchaseOrder, $data['warehouse_id'], $data['received_date']);

        return redirect()->route('grns.edit', $grn)->with('success', "GRN {$grn->number} started. Count the goods, then enter received and damaged quantities.");
    }

    public function show(Grn $grn)
    {
        $grn->load(['supplier', 'purchaseOrder', 'warehouse', 'items.variant.product.brand', 'user']);

        return view('purchasing.grns.show', ['grn' => $grn, 'byProduct' => $grn->items->groupBy(fn ($i) => $i->variant->product_id)]);
    }

    public function edit(Grn $grn)
    {
        if ($grn->status === 'posted') {
            return redirect()->route('grns.show', $grn);
        }
        $grn->load(['supplier', 'purchaseOrder', 'warehouse', 'items.variant.product.brand']);

        return view('purchasing.grns.edit', ['grn' => $grn, 'byProduct' => $grn->items->groupBy(fn ($i) => $i->variant->product_id)]);
    }

    public function update(Request $request, Grn $grn)
    {
        abort_if($grn->status === 'posted', 403, 'Posted GRNs cannot be changed.');
        $request->validate([
            'received' => 'array', 'received.*' => 'nullable|integer|min:0',
            'damaged' => 'array', 'damaged.*' => 'nullable|integer|min:0',
            'received_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $grn) {
            $grn->update($request->only('received_date', 'notes'));
            foreach ($grn->items as $item) {
                $received = (int) $request->input("received.{$item->id}", 0);
                $damaged = min($received, (int) $request->input("damaged.{$item->id}", 0));
                $item->update(['received_qty' => $received, 'damaged_qty' => $damaged]);
            }
            if ($request->input('action') === 'post') {
                PurchaseService::postGrn($grn->fresh());
            }
        });

        if ($request->input('action') === 'post') {
            return redirect()->route('grns.show', $grn)->with('success', "GRN {$grn->number} posted. Stock and landed cost updated.");
        }

        return back()->with('success', 'GRN draft saved.');
    }
}
