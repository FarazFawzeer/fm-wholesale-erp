<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ExpenseCategory;
use App\Models\Warehouse;
use Illuminate\Http\Request;

/** Small lookup lists: brands, categories (with markup rule), stores, expense categories. */
class SetupController extends Controller
{
    protected array $types = [
        'brands' => [Brand::class, 'Brand', ['name' => 'required|max:100', 'country' => 'nullable|max:100']],
        'categories' => [Category::class, 'Category', ['name' => 'required|max:100', 'markup_percent' => 'nullable|numeric|min:0|max:500']],
        'warehouses' => [Warehouse::class, 'Store / warehouse', ['code' => 'required|max:20', 'name' => 'required|max:100', 'address' => 'nullable|max:255']],
        'expense-categories' => [ExpenseCategory::class, 'Expense category', ['name' => 'required|max:100']],
    ];

    public function index()
    {
        return view('masters.setup', [
            'brands' => Brand::withCount('products')->orderBy('name')->get(),
            'categories' => Category::withCount('products')->orderBy('name')->get(),
            'warehouses' => Warehouse::orderBy('id')->get(),
            'expenseCategories' => ExpenseCategory::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, string $type)
    {
        [$model, $label, $rules] = $this->type($type);
        $model::create($request->validate($rules));

        return redirect()->route('setup.index', ['tab' => $type])->with('success', "{$label} added.");
    }

    public function update(Request $request, string $type, int $id)
    {
        [$model, $label, $rules] = $this->type($type);
        $model::findOrFail($id)->update($request->validate($rules));

        return redirect()->route('setup.index', ['tab' => $type])->with('success', "{$label} updated.");
    }

    public function destroy(string $type, int $id)
    {
        [$model, $label] = $this->type($type);
        try {
            $model::findOrFail($id)->delete();
        } catch (\Throwable $e) {
            return redirect()->route('setup.index', ['tab' => $type])->withErrors("{$label} is in use and cannot be deleted.");
        }

        return redirect()->route('setup.index', ['tab' => $type])->with('success', "{$label} deleted.");
    }

    protected function type(string $type): array
    {
        abort_unless(isset($this->types[$type]), 404);

        return $this->types[$type];
    }
}
