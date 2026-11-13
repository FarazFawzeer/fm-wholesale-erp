<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Cheque;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Services\PaymentService;
use App\Services\TradeService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/** Sample shop data for demos: products with colours, purchases, sales, payments and dues. */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        mt_srand(42);
        Auth::login(User::where('type', 'Super Admin')->first());

        User::updateOrCreate(['email' => 'sales@fmwholesale.com'], [
            'name' => 'Nimal Perera', 'type' => 'Sales', 'active' => true, 'password' => Hash::make('password'), 'email_verified_at' => now(),
        ]);

        // ---------- Lists ----------
        $brands = collect(['Comfort Step', 'Urban Walk', 'Lady Grace', 'Kids Joy', 'Classic Leather'])->mapWithKeys(fn ($b) => [$b => Brand::create(['name' => $b])->id]);
        $cats = collect(['Gents Boots', 'Casual Sneakers', 'Ladies Shoes', 'Sports Shoes', 'Kids Shoes'])->mapWithKeys(fn ($c) => [$c => Category::create(['name' => $c])->id]);
        $exp = collect(['Rent', 'Electricity', 'Salaries', 'Transport', 'Tea & Food', 'Other'])->mapWithKeys(fn ($c) => [$c => ExpenseCategory::create(['name' => $c])->id]);

        // ---------- Products: item no, name, brand, type, size from-to, selling price, cost, colours, photo ----------
        $items = [
            ['ARD-1001', 'Classic Chelsea Boot', 'Classic Leather', 'Gents Boots', 39, 45, 7850, 5200, ['Brown', 'Black', 'Tan'], 'chelsea'],
            ['ARD-1002', 'Premium Leather Chelsea', 'Classic Leather', 'Gents Boots', 39, 44, 9450, 6300, ['Coffee', 'Black'], 'chelsea-2'],
            ['ARD-1003', 'Retro Court Sneaker', 'Urban Walk', 'Casual Sneakers', 38, 44, 5650, 3600, ['Cream', 'White', 'Black'], 'court'],
            ['ARD-1004', 'Ladies Street Sneaker', 'Lady Grace', 'Ladies Shoes', 35, 40, 5250, 3350, ['Cream', 'Pink', 'White'], 'court-2'],
            ['ARD-1005', 'Suede Low-top Sneaker', 'Urban Walk', 'Casual Sneakers', 39, 44, 6900, 4450, ['Beige', 'Grey', 'Navy'], 'suede'],
            ['ARD-1006', 'Soft Suede Walker', 'Comfort Step', 'Casual Sneakers', 38, 44, 6250, 3950, ['Beige', 'Brown'], 'suede-2'],
            ['ARD-1007', 'Denim Canvas Sneaker', 'Urban Walk', 'Sports Shoes', 36, 43, 4950, 3100, ['Blue', 'Navy', 'White'], 'denim'],
            ['ARD-1008', 'Kids Denim Sneaker', 'Kids Joy', 'Kids Shoes', 28, 35, 3450, 2150, ['Blue', 'Pink'], 'denim-2'],
        ];

        $products = [];
        foreach ($items as [$code, $name, $brand, $cat, $from, $to, $price, $cost, $colours, $photo]) {
            $p = Product::create([
                'code' => $code, 'name' => $name, 'brand_id' => $brands[$brand], 'category_id' => $cats[$cat],
                'size_from' => $from, 'size_to' => $to, 'selling_price' => $price, 'low_stock_level' => 18,
                'image_path' => 'images/demo-products/' . $photo . '.jpg',
            ]);
            foreach ($colours as $c) {
                $p->colours()->create(['colour' => $c, 'qty' => 0]);
            }
            $products[] = ['model' => $p->load('colours'), 'cost' => $cost];
        }

        // ---------- Suppliers & customers ----------
        $suppliers = collect([
            ['name' => 'Lanka Footwear Importers', 'contact_person' => 'Mr. Fernando', 'phone' => '0112 456 789', 'address' => 'Pettah, Colombo 11'],
            ['name' => 'Golden Step Trading', 'contact_person' => 'Ruwan', 'phone' => '0771 234 567', 'address' => 'Main Street, Colombo 11'],
            ['name' => 'Asia Shoe Suppliers', 'contact_person' => 'Mr. Lee', 'phone' => '0779 876 543', 'address' => 'Wellawatte'],
            ['name' => 'Kandy Leather Works', 'contact_person' => 'Saman', 'phone' => '0812 223 344', 'address' => 'Kandy', 'opening_balance' => 85000],
        ])->map(fn ($s) => Supplier::create($s + ['is_active' => true]));

        $customers = collect([
            ['name' => 'Rizwan Stores', 'shop_name' => 'Rizwan Footwear', 'phone' => '0771112233', 'city' => 'Kandy', 'opening_balance' => 42000],
            ['name' => 'Kumara Shoe Palace', 'phone' => '0712223344', 'city' => 'Kurunegala'],
            ['name' => 'Galle Fashion Centre', 'phone' => '0763334455', 'city' => 'Galle'],
            ['name' => 'Nuwan Shoe Mart', 'phone' => '0754445566', 'city' => 'Negombo'],
            ['name' => 'Fathima Collection', 'shop_name' => 'Fathima Ladies Corner', 'phone' => '0775556677', 'city' => 'Kalmunai'],
            ['name' => 'Jaffna Shoe House', 'phone' => '0216667788', 'city' => 'Jaffna'],
            ['name' => 'Matara Family Shoes', 'phone' => '0417778899', 'city' => 'Matara'],
            ['name' => 'City Kids Shop', 'phone' => '0778889900', 'city' => 'Colombo 06'],
            ['name' => 'Walk-in customer', 'phone' => null, 'city' => null],
        ])->map(fn ($c) => Customer::create($c + ['whatsapp' => $c['phone'], 'is_active' => true]));

        // ---------- Purchases (stock comes in here) ----------
        $buys = [
            // days ago, supplier, products, how paid, transport
            [58, 0, [0, 1, 2, 3], 'paid', 4500],
            [52, 1, [4, 5, 6, 7], 'split', 3000],
            [47, 2, [2, 6, 7], 'part', 0],
            [40, 3, [0, 1, 5], 'unpaid', 2500],
            [26, 1, [3, 2, 4], 'cheque', 0],
            [12, 0, [5, 6, 7], 'unpaid', 0],
        ];
        foreach ($buys as [$daysAgo, $s, $productIdx, $pay, $transport]) {
            $lines = [];
            foreach ($productIdx as $i) {
                $p = $products[$i];
                $lines[] = [
                    'product_id' => $p['model']->id,
                    'price' => $p['cost'],
                    'colours' => $p['model']->colours->map(fn ($c) => ['id' => $c->id, 'qty' => [12, 18, 24, 30, 36, 48][mt_rand(0, 5)]])->all(),
                ];
            }
            $bill = collect($lines)->sum(fn ($l) => $l['price'] * collect($l['colours'])->sum('qty')) + $transport;
            $payments = match ($pay) {
                'paid' => [['method' => 'online', 'amount' => $bill, 'reference' => 'BOC transfer']],
                'split' => [['method' => 'cash', 'amount' => round($bill * 0.4, -2)], ['method' => 'online', 'amount' => $bill - round($bill * 0.4, -2)]],
                'part' => [['method' => 'cash', 'amount' => round($bill * 0.5, -3)]],
                'cheque' => [['method' => 'cheque', 'amount' => round($bill * 0.7, -3), 'cheque_no' => '004512', 'bank' => 'Commercial Bank', 'cheque_date' => now()->addDays(2)->toDateString()]],
                default => [],
            };
            TradeService::purchase(['supplier_id' => $suppliers[$s]->id, 'date' => now()->subDays($daysAgo)->toDateString(),
                'supplier_bill_no' => 'B' . mt_rand(1000, 9999), 'other_costs' => $transport], $lines, $payments);
        }

        // ---------- Sales ----------
        $sales = [
            // days ago, customer, how paid
            [50, 0, 'part'], [46, 1, 'cash'], [44, 2, 'unpaid'], [41, 3, 'split'], [38, 4, 'cash'], [35, 5, 'part'],
            [33, 6, 'cheque'], [30, 1, 'cash'], [27, 7, 'card'], [24, 0, 'unpaid'], [21, 2, 'split'], [18, 8, 'cash'],
            [15, 3, 'part'], [12, 4, 'online'], [9, 6, 'unpaid'], [6, 7, 'split'], [3, 5, 'cash'], [1, 1, 'part'], [0, 8, 'cash'], [0, 2, 'unpaid'],
        ];
        foreach ($sales as [$daysAgo, $ci, $how]) {
            $lines = [];
            foreach ((array) array_rand($products, mt_rand(2, 4)) as $i) {
                $p = $products[$i]['model']->fresh('colours');
                $colours = [];
                foreach ($p->colours as $c) {
                    if ($c->qty >= 6 && mt_rand(0, 2) > 0) {
                        $colours[$c->id] = min($c->qty, [6, 6, 12, 12, 18][mt_rand(0, 4)]);
                    }
                }
                if ($colours) {
                    $lines[] = ['product_id' => $p->id, 'price' => $p->selling_price, 'colours' => $colours];
                }
            }
            if (! $lines) {
                continue;
            }
            $amount = collect($lines)->sum(fn ($l) => $l['price'] * array_sum($l['colours']));
            $discount = $amount > 150000 ? round($amount * 0.03, -2) : 0;
            $total = $amount - $discount;
            $half = round($total * 0.5, -2);
            $payments = match ($how) {
                'cash' => [['method' => 'cash', 'amount' => $total]],
                'card' => [['method' => 'card', 'amount' => $total, 'reference' => 'Visa 4021']],
                'online' => [['method' => 'online', 'amount' => $total, 'reference' => 'Bank transfer']],
                'split' => [['method' => 'cash', 'amount' => $half], ['method' => 'card', 'amount' => $total - $half]],
                'part' => [['method' => 'cash', 'amount' => round($total * 0.4, -3)]],
                'cheque' => [['method' => 'cash', 'amount' => 20000], ['method' => 'cheque', 'amount' => $total - 20000, 'cheque_no' => '10' . mt_rand(1000, 9999),
                    'bank' => 'Sampath Bank', 'cheque_date' => now()->subDays(max(0, $daysAgo - 10))->toDateString()]],
                default => [],
            };
            TradeService::invoice(['customer_id' => $customers[$ci]->id, 'date' => now()->subDays($daysAgo)->toDateString(), 'discount' => $discount], $lines, $payments);
        }

        // Later payments from customers
        foreach ([[0, 30000, 'cash', 20], [5, 25000, 'online', 8]] as [$ci, $amount, $method, $daysAgo]) {
            $c = $customers[$ci];
            $amount = min($amount, $c->balance());
            if ($amount > 0) {
                PaymentService::record('in', 'customer', $c->id, now()->subDays($daysAgo)->toDateString(),
                    [['method' => $method, 'amount' => $amount]], PaymentService::oldestFirst('customer', $c->id, $amount));
            }
        }

        // Old customer cheques have cleared
        Cheque::where('direction', 'received')->whereDate('cheque_date', '<', now()->subDays(5))->get()
            ->each(fn ($ch) => PaymentService::updateCheque($ch, 'cleared', now()->subDays(3)->toDateString()));

        // A customer return
        $inv = Invoice::with('items')->where('customer_id', $customers[3]->id)->first();
        if ($inv) {
            $item = $inv->items->first();
            TradeService::customerReturn($inv, [$item->id => min(2, $item->qty)], ['date' => now()->subDays(10)->toDateString(), 'reason' => 'Damaged']);
        }

        // Part of a supplier's old balance paid
        $s = $suppliers[3];
        PaymentService::record('out', 'supplier', $s->id, now()->subDays(15)->toDateString(), [['method' => 'cash', 'amount' => 50000]],
            PaymentService::oldestFirst('supplier', $s->id, 50000));

        // ---------- Expenses ----------
        foreach ([1, 0] as $m) {
            $base = now()->subMonthsNoOverflow($m)->startOfMonth();
            foreach ([[1, 'Rent', 85000, 'online', 'Building owner'], [5, 'Electricity', 14250, 'cash', 'CEB'], [9, 'Salaries', 120000, 'cash', '3 staff']] as [$d, $cat, $amt, $method, $to]) {
                if ($base->copy()->addDays($d)->lte(now())) {
                    Expense::create(['date' => $base->copy()->addDays($d), 'expense_category_id' => $exp[$cat], 'amount' => $amt, 'method' => $method, 'paid_to' => $to, 'user_id' => auth()->id()]);
                }
            }
        }
        foreach ([3, 7, 11, 16, 22] as $d) {
            Expense::create(['date' => now()->subDays($d), 'expense_category_id' => $exp[$d % 2 ? 'Transport' : 'Tea & Food'],
                'amount' => [3500, 1800, 2400, 950, 4200][$d % 5], 'method' => 'cash', 'user_id' => auth()->id()]);
        }

        Auth::logout();
    }
}
