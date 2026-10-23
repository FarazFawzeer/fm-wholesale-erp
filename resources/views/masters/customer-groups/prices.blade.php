@extends('layouts.vertical', ['subtitle' => $group->name . ' Price List'])

@section('content')
    <x-page-header :title="$group->name . ' - Price List'" :crumbs="['Customer Groups' => route('customer-groups.index'), 'Price list']" />

    <form method="POST" action="{{ route('customer-groups.prices.save', $group) }}">
        @csrf
        <div class="card">
            <div class="card-header small text-muted">Leave a price empty to use the group rule ({{ $group->discount_percent >= 0 ? $group->discount_percent . '% off' : abs($group->discount_percent) . '% above' }} wholesale).</div>
            <div class="card-body table-wrap p-0">
                <div class="table-responsive">
                    <table class="table erp-table stack mb-0">
                        <thead><tr><th>Product</th><th class="text-end">Landed cost</th><th class="text-end">Wholesale</th><th class="text-end">Group rule price</th><th class="text-end">Special price</th></tr></thead>
                        <tbody>
                            @foreach ($products as $p)
                                @php $special = $p->priceListItems->firstWhere('customer_group_id', $group->id); @endphp
                                <tr>
                                    <td class="td-title"><strong>{{ $p->code }}</strong> <span class="small text-muted">{{ $p->brand?->name }} {{ $p->name }} {{ $p->colour }}</span></td>
                                    <td data-label="Cost" class="text-end money">{{ money($p->cost_price) }}</td>
                                    <td data-label="Wholesale" class="text-end money">{{ money($p->selling_price) }}</td>
                                    <td data-label="Rule price" class="text-end money">{{ money(round($p->selling_price * (1 - $group->discount_percent / 100), 2)) }}</td>
                                    <td data-label="Special" class="text-end"><input type="number" step="0.01" name="prices[{{ $p->id }}]" value="{{ $special?->price }}" class="form-control form-control-sm text-end ms-auto" style="max-width: 130px" placeholder="-"></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-end sticky-actions"><button class="btn btn-primary">Save price list</button></div>
        </div>
    </form>
@endsection
