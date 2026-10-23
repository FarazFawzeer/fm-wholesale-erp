# FM Wholesale ERP

Wholesale ERP for **FM Shoes**, an importer and wholesaler of footwear. Built on Laravel 11, Blade, Bootstrap 5 (Taplox admin theme) and MySQL. Every screen works on desktop and on phones.

## Modules

| Area | What it does |
|---|---|
| Import & GRN | Purchase orders in foreign currency, then import costing (freight, insurance, duty, clearing and other charges, shared out by value or quantity), then a GRN (received, damaged and short-shipped pairs per size). Posting the GRN updates stock and the weighted-average landed cost. |
| Inventory | Products by brand, category, colour and size (one SKU per size), stock per store, transfers, adjustments, damaged stock, returns to supplier, low-stock alerts, valuation, CSV bulk import |
| Sales | Quotations, sales orders, invoices (cash or credit), full or partial deliveries, returns and credit notes, printable documents |
| Pricing | Markup rules (product, then category, then company default), customer-group discounts and price lists, bulk price updates, minimum selling price, discount limits for staff |
| Money | Customer receipts, supplier payments, split payments (cash, bank, card, cheque), post-dated cheque tracking with clearance and bounce handling, cash and bank accounts, daily cashbook, expenses |
| Credit control | Credit limits and terms (credit sales are blocked when over the limit or overdue), overdue list with WhatsApp reminders, supplier obligations |
| Reports | Stock valuation, low stock, customer statement, ageing, product and invoice profit, cash flow, expected payments, import costing |

## Demo defaults (until the client confirms)

- Base currency is LKR. Suppliers invoice in USD, with the exchange rate entered per shipment.
- The stock unit is a **pair**. A carton holds a fixed number of pairs, and "Quick fill" spreads cartons evenly across the size run.
- Cost method is **weighted average** landed cost. Import charges are shared **by value** by default.
- Two stores (main warehouse and showroom). No VAT on invoices.

## Setup

```bash
composer install
npm install && npm run build
cp .env.example .env        # set DB_* for MySQL
php artisan key:generate
php artisan migrate:fresh --seed   # creates demo data
php artisan serve
```

## Demo logins (password: `password`)

| Role | Email |
|---|---|
| Super Admin | admin@fmwholesale.com |
| Sales | sales@fmwholesale.com |
| Store | store@fmwholesale.com |
| Accounts | accounts@fmwholesale.com |

Change these passwords before going live.
