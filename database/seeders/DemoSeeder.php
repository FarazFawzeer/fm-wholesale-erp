<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Invoice;
use App\Models\PriceListItem;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\StockAdjustment;
use App\Models\StockTransfer;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\LedgerService;
use App\Services\NumberService;
use App\Services\PaymentService;
use App\Services\PurchaseService;
use App\Services\SalesService;
use App\Services\StockService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Realistic demo data for an imported-footwear wholesaler: three import
 * shipments, sales over the last ~6 weeks, receipts, cheques and expenses.
 */
class DemoSeeder extends Seeder
{
    protected array $products = [];

    public function run(): void
    {
        $admin = User::where('email', 'admin@fmwholesale.com')->first();
        Auth::setUser($admin);

        foreach ([['Nimal Perera', 'sales@fmwholesale.com', 'Sales'], ['Kamal Silva', 'store@fmwholesale.com', 'Store'],
            ['Dilani Fernando', 'accounts@fmwholesale.com', 'Accounts']] as [$name, $email, $type]) {
            User::updateOrCreate(['email' => $email], ['name' => $name, 'type' => $type, 'password' => Hash::make('password'), 'active' => true]);
        }

        $main = Warehouse::create(['code' => 'MAIN', 'name' => 'Main Warehouse - Pettah', 'address' => 'Keyzer Street, Colombo 11', 'is_default' => true]);
        $show = Warehouse::create(['code' => 'SHOW', 'name' => 'Showroom - Maharagama', 'address' => 'High Level Road, Maharagama']);

        $cash = Account::create(['name' => 'Cash in Hand', 'type' => 'cash', 'opening_balance' => 250000]);
        $bank = Account::create(['name' => 'Commercial Bank - Current', 'type' => 'bank', 'bank_name' => 'Commercial Bank', 'account_no' => '1000 2345 6789', 'opening_balance' => 4500000]);
        Account::create(['name' => 'Sampath Bank - Savings', 'type' => 'bank', 'bank_name' => 'Sampath Bank', 'account_no' => '0012 3456 7788', 'opening_balance' => 1200000]);
        $card = Account::create(['name' => 'Card Settlements', 'type' => 'card', 'bank_name' => 'Commercial Bank', 'opening_balance' => 0]);

        foreach (['Rent', 'Salaries & Wages', 'Electricity & Water', 'Transport & Delivery', 'Clearing & Port', 'Office & Stationery', 'Telephone & Internet'] as $c) {
            ExpenseCategory::create(['name' => $c]);
        }

        // ---- Masters ----
        $brands = collect(['Walkmaxx' => 'China', 'UrbanStep' => 'China', 'Sportiva' => 'Vietnam', 'Elegance' => 'Thailand', 'Kidzo' => 'China', 'ComfortPlus' => 'India'])
            ->map(fn ($country, $name) => Brand::create(['name' => $name, 'country' => $country]));
        $cats = collect(["Men's Casual" => 35, "Men's Formal" => 40, 'Ladies Sandals' => 45, 'Ladies Shoes' => 40, 'Sports Shoes' => 30, 'Kids Shoes' => 40, 'Slippers' => 50])
            ->map(fn ($m, $name) => Category::create(['name' => $name, 'markup_percent' => $m]));

        $men = '39,40,41,42,43,44';
        $ladies = '36,37,38,39,40,41';
        $kids = '28,29,30,31,32,33';
        $sport = '39,40,41,42,43,44,45';

        $catalogue = [
            ['WM-1021', 'Walkmaxx', "Men's Casual", 'Canvas Loafer', 'Navy', 'Men', $men, 12],
            ['WM-1022', 'Walkmaxx', "Men's Casual", 'Canvas Loafer', 'Black', 'Men', $men, 12],
            ['WM-2040', 'Walkmaxx', "Men's Formal", 'Oxford Leather', 'Brown', 'Men', $men, 12],
            ['WM-2041', 'Walkmaxx', "Men's Formal", 'Oxford Leather', 'Black', 'Men', $men, 12],
            ['US-3105', 'UrbanStep', "Men's Casual", 'Street Sneaker', 'White', 'Men', $men, 12],
            ['US-3106', 'UrbanStep', "Men's Casual", 'Street Sneaker', 'Grey', 'Men', $men, 12],
            ['SP-5001', 'Sportiva', 'Sports Shoes', 'Runner Pro', 'Black/Red', 'Unisex', $sport, 14],
            ['SP-5002', 'Sportiva', 'Sports Shoes', 'Runner Pro', 'Blue', 'Unisex', $sport, 14],
            ['SP-5010', 'Sportiva', 'Sports Shoes', 'Court Trainer', 'White', 'Unisex', $sport, 14],
            ['EL-7012', 'Elegance', 'Ladies Sandals', 'Block Heel Sandal', 'Beige', 'Ladies', $ladies, 12],
            ['EL-7013', 'Elegance', 'Ladies Sandals', 'Block Heel Sandal', 'Black', 'Ladies', $ladies, 12],
            ['EL-7101', 'Elegance', 'Ladies Shoes', 'Pointed Flat', 'Nude', 'Ladies', $ladies, 12],
            ['KZ-9001', 'Kidzo', 'Kids Shoes', 'School Shoe', 'Black', 'Kids', $kids, 12],
            ['KZ-9020', 'Kidzo', 'Kids Shoes', 'Light-Up Sneaker', 'Pink', 'Kids', $kids, 12],
            ['CP-6001', 'ComfortPlus', 'Slippers', 'Cushion Slipper', 'Brown', 'Men', $men, 24],
            ['CP-6002', 'ComfortPlus', 'Slippers', 'Cushion Slipper', 'Maroon', 'Ladies', $ladies, 24],
        ];

        foreach ($catalogue as [$code, $brand, $cat, $name, $colour, $gender, $sizes, $ppc]) {
            $p = Product::create([
                'code' => $code, 'name' => $name, 'brand_id' => $brands[$brand]->id, 'category_id' => $cats[$cat]->id,
                'colour' => $colour, 'gender' => $gender, 'sizes' => $sizes, 'pairs_per_carton' => $ppc,
                'reorder_level' => 36,
            ]);
            foreach (explode(',', $sizes) as $size) {
                $p->variants()->create(['size' => $size, 'sku' => $code . '-' . $size]);
            }
            $this->products[$code] = $p->load('variants');
        }

        $suppliers = [
            'GZ' => Supplier::create(['code' => 'SUP-001', 'name' => 'Guangzhou Hengtai Footwear Co.', 'country' => 'China', 'currency' => 'USD', 'contact_person' => 'Lily Chen', 'phone' => '+86 20 8888 1234', 'email' => 'sales@hengtai.example', 'payment_terms_days' => 45]),
            'JJ' => Supplier::create(['code' => 'SUP-002', 'name' => 'Jinjiang Sports Shoes Ltd', 'country' => 'China', 'currency' => 'USD', 'contact_person' => 'Wang Jun', 'phone' => '+86 595 8522 7788', 'payment_terms_days' => 30]),
            'BK' => Supplier::create(['code' => 'SUP-003', 'name' => 'Bangkok Fashion Footwear', 'country' => 'Thailand', 'currency' => 'USD', 'contact_person' => 'Somchai K.', 'phone' => '+66 2 555 0199', 'payment_terms_days' => 30]),
            'IN' => Supplier::create(['code' => 'SUP-004', 'name' => 'Agra Comfort Footwear Pvt Ltd', 'country' => 'India', 'currency' => 'USD', 'contact_person' => 'Rahul Mehta', 'phone' => '+91 562 222 3344', 'payment_terms_days' => 30]),
        ];

        $groups = [
            'key' => CustomerGroup::create(['name' => 'Key Accounts', 'discount_percent' => 7, 'description' => 'Large buyers - best wholesale price']),
            'std' => CustomerGroup::create(['name' => 'Standard Wholesale', 'discount_percent' => 0, 'description' => 'Normal wholesale price list']),
            'small' => CustomerGroup::create(['name' => 'Small Retailers', 'discount_percent' => -5, 'description' => 'Small shops buying few cartons']),
        ];

        $customerRows = [
            ['Fashion Corner', 'Fashion Corner (Pvt) Ltd', 'key', '0771234567', 'Main Street', 'Kandy', 3500000, 30],
            ['Step In Shoes', 'Step In Shoes', 'key', '0712345678', 'Galle Road', 'Galle', 2000000, 30],
            ['City Footwear', 'City Footwear Centre', 'std', '0763456789', 'Negombo Road', 'Negombo', 1200000, 30],
            ['Lanka Shoe Palace', 'Lanka Shoe Palace', 'std', '0754567890', 'Hospital Road', 'Jaffna', 1000000, 45],
            ['Sunrise Traders', 'Sunrise Traders', 'std', '0705678901', 'Main Street', 'Kurunegala', 800000, 21],
            ['Royal Shoe Mart', 'Royal Shoe Mart', 'std', '0776789012', 'Station Road', 'Matara', 900000, 21],
            ['Kids World', 'Kids World Stores', 'small', '0717890123', 'Bazaar Street', 'Badulla', 600000, 14],
            ['Happy Feet', 'Happy Feet', 'small', '0768901234', 'Main Street', 'Anuradhapura', 500000, 7],
            ['Walk-in Customer', null, 'small', null, null, 'Colombo', 0, 0],
        ];
        $customers = [];
        foreach ($customerRows as $i => [$name, $biz, $g, $phone, $addr, $city, $limit, $terms]) {
            $customers[$name] = Customer::create([
                'code' => 'CUS-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT), 'name' => $name, 'business_name' => $biz,
                'customer_group_id' => $groups[$g]->id, 'phone' => $phone, 'whatsapp' => $phone, 'address' => $addr, 'city' => $city,
                'credit_limit' => $limit, 'payment_terms_days' => $terms,
            ]);
        }

        // ---- Shipment 1: fully received ~7 weeks ago ----
        $po1 = $this->purchaseOrder($suppliers['GZ'], -60, 302.50, [
            ['WM-1021', 8.40, 6], ['WM-1022', 8.40, 6], ['WM-2040', 14.20, 4], ['WM-2041', 14.20, 5], ['US-3105', 11.00, 6], ['US-3106', 11.00, 4],
        ], ['freight' => 385000, 'insurance' => 28500, 'duty' => 812000, 'clearing' => 96000, 'transport' => 35000], 'MSKU 7712093');
        $grn1 = PurchaseService::draftGrn($po1, $main->id, now()->subDays(46)->toDateString());
        $grn1->items->take(3)->each(fn ($i) => $i->update(['damaged_qty' => 1]));
        PurchaseService::postGrn($grn1->fresh());

        // ---- Shipment 2: sports + ladies, partially received ----
        $po2 = $this->purchaseOrder($suppliers['JJ'], -40, 300.00, [
            ['SP-5001', 12.50, 5], ['SP-5002', 12.50, 4], ['SP-5010', 10.80, 4], ['KZ-9001', 6.20, 6], ['KZ-9020', 7.40, 4],
        ], ['freight' => 298000, 'insurance' => 21000, 'duty' => 515000, 'clearing' => 82000, 'transport' => 28000], 'TGHU 5530127');
        $grn2 = PurchaseService::draftGrn($po2, $main->id, now()->subDays(24)->toDateString());
        $grn2->items->filter(fn ($i) => $i->variant->product->code === 'KZ-9020')->each(fn ($i) => $i->update(['received_qty' => (int) floor($i->ordered_qty / 2)]));
        $grn2->items->filter(fn ($i) => $i->variant->product->code === 'SP-5001')->take(2)->each(fn ($i) => $i->update(['damaged_qty' => 2]));
        PurchaseService::postGrn($grn2->fresh());

        $po3 = $this->purchaseOrder($suppliers['BK'], -30, 298.00, [
            ['EL-7012', 9.60, 5], ['EL-7013', 9.60, 5], ['EL-7101', 8.90, 4],
        ], ['freight' => 142000, 'insurance' => 11000, 'duty' => 268000, 'clearing' => 54000, 'transport' => 18000], 'BKK-AIR-4471');
        PurchaseService::postGrn(PurchaseService::draftGrn($po3, $main->id, now()->subDays(18)->toDateString()));

        $po4 = $this->purchaseOrder($suppliers['IN'], -50, 299.00, [['CP-6001', 3.10, 4], ['CP-6002', 3.10, 4]],
            ['freight' => 62000, 'insurance' => 4000, 'duty' => 88000, 'clearing' => 25000], 'INMAA 22071');
        PurchaseService::postGrn(PurchaseService::draftGrn($po4, $main->id, now()->subDays(36)->toDateString()));

        // In transit (costed, not received) and a draft order
        $this->purchaseOrder($suppliers['GZ'], -8, 304.00, [['WM-1021', 8.50, 8], ['US-3105', 11.20, 8], ['WM-2041', 14.40, 4]],
            ['freight' => 360000, 'insurance' => 30000, 'duty' => 0], 'MSKU 8820415', 'costed');
        $this->purchaseOrder($suppliers['JJ'], -1, 300.00, [['SP-5010', 10.90, 6], ['KZ-9001', 6.30, 8]], [], null, 'draft');

        // Selling prices from markup rules + a few price-list overrides
        foreach (Product::all() as $p) {
            $p->update(['selling_price' => $p->suggestedPrice()]);
        }
        PriceListItem::create(['customer_group_id' => $groups['key']->id, 'product_id' => $this->products['SP-5001']->id, 'price' => round($this->products['SP-5001']->fresh()->selling_price * 0.9, -1)]);
        PriceListItem::create(['customer_group_id' => $groups['key']->id, 'product_id' => $this->products['WM-2040']->id, 'price' => round($this->products['WM-2040']->fresh()->selling_price * 0.9, -1)]);

        // Transfer to showroom + damaged adjustment
        $this->transfer($main, $show, -15, [['WM-1021', 1], ['US-3105', 2], ['EL-7012', 2], ['KZ-9001', 2]]);
        $this->adjust($main, -10, 'damaged', [['WM-1022', '41', 2]], 'Water damage in store room');

        // ---- Sales ----
        $sales = [
            [-44, 'Fashion Corner', 'credit', [['WM-1021', 2], ['WM-2040', 1], ['US-3105', 1]], true],
            [-40, 'Step In Shoes', 'credit', [['WM-1022', 2], ['US-3106', 1]], true],
            [-38, 'City Footwear', 'credit', [['WM-2041', 1], ['WM-1021', 1]], true],
            [-35, 'Walk-in Customer', 'cash', [['US-3105', 0.5]], true],
            [-33, 'Lanka Shoe Palace', 'credit', [['WM-1022', 1], ['CP-6001', 1]], true],
            [-28, 'Sunrise Traders', 'credit', [['CP-6002', 1], ['WM-1021', 1]], true],
            [-25, 'Royal Shoe Mart', 'credit', [['US-3106', 1], ['WM-2040', 1]], true],
            [-21, 'Fashion Corner', 'credit', [['SP-5001', 1], ['SP-5002', 1], ['KZ-9001', 1]], true],
            [-19, 'Kids World', 'credit', [['KZ-9001', 1], ['KZ-9020', 0.5]], true],
            [-16, 'Step In Shoes', 'credit', [['EL-7012', 1], ['EL-7013', 1], ['SP-5010', 1]], true],
            [-12, 'Happy Feet', 'credit', [['EL-7101', 1], ['CP-6002', 1]], true],
            [-9, 'City Footwear', 'credit', [['SP-5002', 1], ['EL-7013', 1]], false],
            [-6, 'Walk-in Customer', 'cash', [['EL-7012', 0.5], ['CP-6001', 0.5]], true],
            [-4, 'Fashion Corner', 'credit', [['WM-2041', 1], ['SP-5010', 1], ['EL-7101', 1]], 'partial'],
            [-2, 'Sunrise Traders', 'credit', [['SP-5001', 1]], true],
            [-1, 'Royal Shoe Mart', 'credit', [['KZ-9001', 1], ['EL-7012', 1]], false],
            [0, 'Step In Shoes', 'credit', [['US-3105', 1], ['EL-7101', 0.5]], true],
            [0, 'Walk-in Customer', 'cash', [['SP-5010', 0.5]], true],
        ];
        $invoices = [];
        foreach ($sales as $n => [$day, $cust, $type, $items, $deliver]) {
            $customer = $customers[$cust];
            $lines = [];
            foreach ($items as [$code, $cartons]) {
                $p = $this->products[$code]->fresh(['variants', 'priceListItems']);
                $lines[] = ['product_id' => $p->id, 'unit_price' => $p->priceForGroup($customer->group), 'discount_percent' => $n % 5 === 0 ? 2 : 0, 'qty' => $this->cartonQty($p, $cartons * 2)];
            }
            $date = now()->addDays($day)->toDateString();
            $pay = [];
            if ($type === 'cash') {
                [$rows, , , $total] = SalesService::buildLines($lines, false);
                $pay = [['method' => 'cash', 'amount' => $total * 0.6, 'account_id' => $cash->id], ['method' => 'card', 'amount' => $total - $total * 0.6, 'account_id' => $card->id, 'reference' => 'VISA ****4421']];
            }
            $invoice = SalesService::createInvoice(['customer_id' => $customer->id, 'warehouse_id' => $main->id, 'date' => $date,
                'sale_type' => $type, 'override_credit' => true], $lines, $deliver === true, $pay);
            if ($deliver === 'partial') {
                $invoice->load('lines');
                SalesService::deliver($invoice, $invoice->lines->mapWithKeys(fn ($l) => [$l->id => (int) ceil($l->qty / 2)])->all(), ['date' => $date, 'vehicle_no' => 'LB-4521']);
            }
            $invoices[] = $invoice;
        }

        // ---- Receipts & cheques ----
        $receipt = fn ($inv, $date, $lines) => PaymentService::record('in', 'customer', $inv->customer_id, now()->addDays($date)->toDateString(), $lines, [$inv->id => array_sum(array_column($lines, 'amount'))]);
        $receipt($invoices[0], -20, [['method' => 'bank_transfer', 'amount' => $invoices[0]->total, 'account_id' => $bank->id, 'reference' => 'CEFT 88213']]);
        $receipt($invoices[1], -15, [['method' => 'cheque', 'amount' => $invoices[1]->total, 'cheque_no' => '204561', 'bank' => 'HNB', 'cheque_date' => now()->subDays(12)->toDateString()]]);
        $receipt($invoices[2], -10, [['method' => 'cash', 'amount' => round($invoices[2]->total * 0.5, -2), 'account_id' => $cash->id]]);
        $receipt($invoices[4], -8, [['method' => 'cheque', 'amount' => round($invoices[4]->total * 0.6, -2), 'cheque_no' => '778120', 'bank' => 'BOC', 'cheque_date' => now()->addDays(12)->toDateString()]]);
        $receipt($invoices[6], -6, [['method' => 'cheque', 'amount' => $invoices[6]->total, 'cheque_no' => '330915', 'bank' => 'Sampath', 'cheque_date' => now()->subDays(3)->toDateString()]]);
        $receipt($invoices[7], -5, [['method' => 'cheque', 'amount' => $invoices[7]->total, 'cheque_no' => '501288', 'bank' => 'Commercial', 'cheque_date' => now()->addDays(25)->toDateString()]]);
        $receipt($invoices[9], -3, [['method' => 'cash', 'amount' => 250000, 'account_id' => $cash->id], ['method' => 'bank_transfer', 'amount' => round($invoices[9]->total - 250000, 2), 'account_id' => $bank->id, 'reference' => 'Online 55102']]);
        $receipt($invoices[5], -2, [['method' => 'cheque', 'amount' => round($invoices[5]->total * 0.5, -2), 'cheque_no' => '611407', 'bank' => 'NSB', 'cheque_date' => now()->addDays(5)->toDateString()]]);

        $cheques = \App\Models\Cheque::orderBy('id')->get();
        PaymentService::updateChequeStatus($cheques[0], 'cleared', now()->subDays(10)->toDateString(), $bank->id);
        PaymentService::updateChequeStatus($cheques[2], 'bounced', now()->subDays(1)->toDateString(), $bank->id, 'Refer to drawer');

        // Sales return on an earlier invoice
        $invoices[2]->load('lines');
        SalesService::creditNote($invoices[2], [$invoices[2]->lines->first()->id => 2], ['date' => now()->subDays(9)->toDateString(), 'reason' => 'Wrong size sent', 'restock' => true]);

        // Quotation + sales order
        $q = SalesService::saveSalesOrder(null, ['type' => 'quotation', 'customer_id' => $customers['Lanka Shoe Palace']->id, 'date' => now()->subDays(2)->toDateString(), 'valid_until' => now()->addDays(12)->toDateString(), 'notes' => 'Season opening order - prices valid 14 days'],
            [['product_id' => $this->products['SP-5001']->id, 'unit_price' => $this->products['SP-5001']->fresh()->selling_price, 'discount_percent' => 3, 'qty' => $this->cartonQty($this->products['SP-5001'], 2)],
             ['product_id' => $this->products['EL-7013']->id, 'unit_price' => $this->products['EL-7013']->fresh()->selling_price, 'discount_percent' => 0, 'qty' => $this->cartonQty($this->products['EL-7013'], 1)]]);
        SalesService::saveSalesOrder(null, ['type' => 'order', 'customer_id' => $customers['Step In Shoes']->id, 'date' => now()->toDateString(), 'notes' => 'Deliver before Friday'],
            [['product_id' => $this->products['WM-2040']->id, 'unit_price' => $this->products['WM-2040']->fresh()->priceForGroup($groups['key']), 'discount_percent' => 0, 'qty' => $this->cartonQty($this->products['WM-2040'], 1)]]);

        // ---- Supplier payments ----
        PaymentService::record('out', 'supplier', $suppliers['GZ']->id, now()->subDays(30)->toDateString(),
            [['method' => 'bank_transfer', 'amount' => round($po1->fresh()->payable_amount * 0.5, -3), 'account_id' => $bank->id, 'reference' => 'TT 2209871']], [$po1->id => round($po1->fresh()->payable_amount * 0.5, -3)]);
        PaymentService::record('out', 'supplier', $suppliers['GZ']->id, now()->subDays(1)->toDateString(),
            [['method' => 'cheque', 'amount' => 1800000, 'account_id' => $bank->id, 'cheque_no' => '000417', 'bank' => 'Commercial', 'cheque_date' => now()->addDays(9)->toDateString()]], [$po1->id => 1800000]);
        PaymentService::record('out', 'supplier', $suppliers['IN']->id, now()->subDays(20)->toDateString(),
            [['method' => 'bank_transfer', 'amount' => $po4->fresh()->payable_amount, 'account_id' => $bank->id, 'reference' => 'TT 2210044']], [$po4->id => $po4->fresh()->payable_amount]);

        // ---- Expenses ----
        foreach ([[-28, 'Rent', $bank, 185000, 'Pettah warehouse rent'], [-27, 'Rent', $bank, 95000, 'Showroom rent'],
            [-25, 'Salaries & Wages', $bank, 420000, 'Staff salaries'], [-18, 'Electricity & Water', $cash, 28500, 'CEB bill'],
            [-14, 'Transport & Delivery', $cash, 18500, 'Lorry hire - Galle delivery'], [-7, 'Transport & Delivery', $cash, 12000, 'Kandy delivery'],
            [-5, 'Office & Stationery', $cash, 7800, 'Invoice books, printer ink'], [-3, 'Telephone & Internet', $bank, 9900, 'Dialog fibre']] as [$d, $cat, $acc, $amt, $desc]) {
            $e = Expense::create(['number' => NumberService::next('EXP'), 'date' => now()->addDays($d)->toDateString(),
                'expense_category_id' => ExpenseCategory::where('name', $cat)->value('id'), 'account_id' => $acc->id, 'amount' => $amt, 'description' => $desc, 'user_id' => $admin->id]);
            LedgerService::post($acc->id, $e->date, 'expense', $cat . ': ' . $desc, -$amt, $e);
        }
        LedgerService::post($cash->id, now()->subDays(4)->toDateString(), 'transfer', 'Cash deposited to Commercial Bank', -300000);
        LedgerService::post($bank->id, now()->subDays(4)->toDateString(), 'transfer', 'Cash deposit from cash in hand', 300000);
    }

    protected function purchaseOrder(Supplier $supplier, int $day, float $rate, array $items, array $charges, ?string $ref, string $status = 'ordered'): PurchaseOrder
    {
        $po = PurchaseOrder::create([
            'number' => NumberService::next('PO'), 'supplier_id' => $supplier->id, 'order_date' => now()->addDays($day)->toDateString(),
            'expected_date' => now()->addDays($day + 30)->toDateString(), 'currency' => 'USD', 'exchange_rate' => $rate,
            'allocation_method' => 'value', 'shipment_ref' => $ref, 'status' => $status, 'user_id' => Auth::id(),
        ]);
        $lines = [];
        foreach ($items as [$code, $price, $cartons]) {
            $lines[] = ['product_id' => $this->products[$code]->id, 'unit_price' => $price, 'qty' => $this->cartonQty($this->products[$code], $cartons * 4)];
        }
        PurchaseService::syncItems($po, $lines);
        PurchaseService::syncCharges($po, collect($charges)->map(fn ($amount, $type) => ['type' => $type, 'amount' => $amount])->values()->all());
        if ($charges && $status === 'ordered') {
            $po->update(['status' => 'costed']);
        }

        return $po->fresh(['items']);
    }

    /** Spread N cartons evenly across the size run (e.g. 1 carton of 12 over 6 sizes = 2 pairs each). */
    protected function cartonQty(Product $p, float $cartons): array
    {
        $variants = $p->variants;
        $pairs = (int) round($p->pairs_per_carton * $cartons);
        $out = [];
        foreach ($variants as $i => $v) {
            $out[$v->id] = intdiv($pairs, $variants->count()) + ($i < $pairs % $variants->count() ? 1 : 0);
        }

        return $out;
    }

    protected function transfer(Warehouse $from, Warehouse $to, int $day, array $items): void
    {
        $t = StockTransfer::create(['number' => NumberService::next('TRF'), 'from_warehouse_id' => $from->id, 'to_warehouse_id' => $to->id,
            'date' => now()->addDays($day)->toDateString(), 'notes' => 'Showroom display stock', 'user_id' => Auth::id()]);
        foreach ($items as [$code, $cartons]) {
            $p = $this->products[$code];
            foreach ($this->cartonQty($p, $cartons) as $vid => $q) {
                StockService::move($vid, $from->id, -$q, 'transfer_out', $p->fresh()->cost_price, $t, null, $t->date);
                StockService::move($vid, $to->id, $q, 'transfer_in', $p->fresh()->cost_price, $t, null, $t->date);
                $t->items()->create(['product_variant_id' => $vid, 'qty' => $q, 'unit_cost' => $p->fresh()->cost_price]);
            }
        }
    }

    protected function adjust(Warehouse $wh, int $day, string $reason, array $items, string $notes): void
    {
        $a = StockAdjustment::create(['number' => NumberService::next('ADJ'), 'warehouse_id' => $wh->id, 'date' => now()->addDays($day)->toDateString(),
            'reason' => $reason, 'notes' => $notes, 'user_id' => Auth::id()]);
        $value = 0;
        foreach ($items as [$code, $size, $q]) {
            $p = $this->products[$code]->fresh();
            $vid = $p->variants->firstWhere('size', $size)->id;
            StockService::move($vid, $wh->id, -$q, 'adjustment', $p->cost_price, $a, $notes, $a->date);
            StockService::moveDamaged($vid, $wh->id, $q, 'adjustment', $p->cost_price, $a, $notes, $a->date);
            $a->items()->create(['product_variant_id' => $vid, 'qty' => -$q, 'unit_cost' => $p->cost_price]);
            $value += $q * $p->cost_price;
        }
        $a->update(['value' => $value]);
    }
}
