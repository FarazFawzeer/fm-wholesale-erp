<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\ExpenseCategory;
use App\Support\Settings;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    protected const LISTS = [
        'brands' => [Brand::class, 'Brands', 'products'],
        'categories' => [Category::class, 'Product types', 'products'],
        'expense-categories' => [ExpenseCategory::class, 'Expense types', 'expenses'],
    ];

    public function edit()
    {
        return view('settings.edit', [
            's' => Settings::all(),
            'terms' => terms_list(),
            'lists' => collect(self::LISTS)->map(fn ($l, $key) => ['key' => $key, 'title' => $l[1], 'items' => $l[0]::withCount($l[2])->orderBy('name')->get(), 'relation' => $l[2]]),
        ]);
    }

    public function update(Request $request)
    {
        abort_unless($request->user()->isManager(), 403, 'Only managers can change settings.');
        $data = $request->validate([
            'company_name' => 'required|max:100',
            'company_tagline' => 'nullable|max:255',
            'company_address' => 'nullable|max:255',
            'company_phone' => 'nullable|max:100',
            'item_prefix' => ['nullable', 'max:10', 'regex:/^[A-Za-z0-9]*$/'],
        ]);
        $data['item_prefix'] = strtoupper($data['item_prefix'] ?? '') ?: 'ARD';
        Settings::set($data);

        return back()->with('success', 'Settings saved.');
    }

    /** Terms & conditions: one point per row, printed as a numbered list on every invoice. */
    public function terms(Request $request)
    {
        abort_unless($request->user()->isManager(), 403, 'Only managers can change settings.');
        $request->validate(['terms' => 'array|max:30', 'terms.*' => 'nullable|max:300'], [], ['terms.*' => 'point']);
        $points = collect($request->input('terms', []))
            ->map(fn ($t) => trim(preg_replace('/^\s*\d+[\.\)]\s*/', '', preg_replace('/\s+/', ' ', (string) $t))))
            ->filter()->values();
        Settings::set(['invoice_terms' => $points->implode("\n")]);

        return back()->with('success', 'Terms & conditions saved (' . $points->count() . ' ' . \Illuminate\Support\Str::plural('point', $points->count()) . ').');
    }

    public function storeItem(Request $request, string $type)
    {
        [$model] = self::LISTS[$type] ?? abort(404);
        $data = $request->validate(['name' => 'required|max:100']);
        $model::firstOrCreate(['name' => trim($data['name'])]);

        return back()->with('success', 'Added "' . $data['name'] . '".');
    }

    public function updateItem(Request $request, string $type, int $id)
    {
        [$model] = self::LISTS[$type] ?? abort(404);
        $data = $request->validate(['name' => 'required|max:100']);
        $model::findOrFail($id)->update(['name' => trim($data['name'])]);

        return back()->with('success', 'Renamed.');
    }

    public function destroyItem(string $type, int $id)
    {
        [$model, , $relation] = self::LISTS[$type] ?? abort(404);
        $item = $model::withCount($relation)->findOrFail($id);
        if ($item->{$relation . '_count'} > 0) {
            return back()->withErrors(['name' => '"' . $item->name . '" is in use and cannot be deleted.']);
        }
        $item->delete();

        return back()->with('success', 'Deleted.');
    }
}
