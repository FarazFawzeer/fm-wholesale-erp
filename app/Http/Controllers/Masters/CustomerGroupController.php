<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\CustomerGroup;
use App\Models\PriceListItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CustomerGroupController extends Controller
{
    public function index()
    {
        return view('masters.customer-groups.index', [
            'groups' => CustomerGroup::withCount(['customers', 'priceListItems'])->orderBy('id')->get(),
        ]);
    }

    public function store(Request $request)
    {
        CustomerGroup::create($this->validated($request));

        return back()->with('success', 'Customer group added.');
    }

    public function update(Request $request, CustomerGroup $customerGroup)
    {
        $customerGroup->update($this->validated($request, $customerGroup));

        return back()->with('success', 'Customer group updated.');
    }

    /** Group price list: special price per product, otherwise the group discount applies. */
    public function prices(CustomerGroup $customerGroup)
    {
        return view('masters.customer-groups.prices', [
            'group' => $customerGroup,
            'products' => Product::with(['brand', 'priceListItems'])->where('is_active', true)->orderBy('code')->get(),
        ]);
    }

    public function savePrices(Request $request, CustomerGroup $customerGroup)
    {
        foreach ($request->input('prices', []) as $productId => $price) {
            if ($price === null || $price === '') {
                PriceListItem::where('customer_group_id', $customerGroup->id)->where('product_id', $productId)->delete();
            } else {
                PriceListItem::updateOrCreate(['customer_group_id' => $customerGroup->id, 'product_id' => $productId], ['price' => (float) $price]);
            }
        }

        return back()->with('success', 'Price list saved for ' . $customerGroup->name . '.');
    }

    protected function validated(Request $request, ?CustomerGroup $group = null): array
    {
        return $request->validate([
            'name' => 'required|max:100|unique:customer_groups,name' . ($group ? ',' . $group->id : ''),
            'discount_percent' => 'required|numeric|min:-100|max:100',
            'description' => 'nullable|max:255',
        ]);
    }
}
