@extends('layouts.vertical', ['subtitle' => 'Missing pairs'])

@section('content')
    <x-page-header title="Reports">
        <button type="button" class="btn btn-soft-secondary" onclick="window.print()"><iconify-icon icon="solar:printer-outline" class="align-middle me-1"></iconify-icon>Print</button>
    </x-page-header>
    @include('reports.tabs')
    @include('reports.partials.range')

    <div class="row g-3 mb-3">
        <div class="col-6 col-xl-4"><x-kpi label="Pairs not received" :value="qty($pairs)" :sub="$billed ? 'of ' . qty($billed) . ' pairs bought (' . round($pairs / $billed * 100, 1) . '%)' : null" icon="solar:box-minimalistic-outline" color="danger" /></div>
        <div class="col-6 col-xl-4"><x-kpi label="Value lost (cost)" :value="money($value)" icon="solar:wallet-outline" color="warning" /></div>
        <div class="col-12 col-xl-4">
            <div class="card kpi-card h-100 mb-0"><div class="card-body d-block">
                <div class="kpi-label mb-1">By reason</div>
                @forelse ($byReason as $reason => $r)
                    <div class="d-flex justify-content-between small"><span>{{ $reason }}</span><b>{{ $r['pairs'] }} prs &middot; {{ money($r['value']) }}</b></div>
                @empty
                    <div class="small text-muted">Nothing missing in this period.</div>
                @endforelse
            </div></div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Pairs paid for but not received</h5></div>
                <div class="card-body table-wrap p-0">
                    <div class="table-responsive">
                        <table class="table erp-table stack mb-0">
                            <thead><tr><th>Date</th><th>Product</th><th>Supplier</th><th class="text-end">Bought</th><th class="text-end">Missing</th><th>Reason</th><th class="text-end">Value</th></tr></thead>
                            <tbody>
                                @forelse ($rows as $it)
                                    <tr>
                                        <td data-label="Date" class="text-nowrap"><a href="{{ route('purchases.show', $it->purchase) }}" class="doc-link">{{ $it->purchase->number }}</a><small class="d-block text-muted">{{ $it->purchase->date->format('d M Y') }}</small></td>
                                        <td class="td-title"><span class="fw-semibold">{{ $it->product->name }}</span> <small class="text-muted">{{ $it->colour->title }}</small></td>
                                        <td data-label="Supplier">{{ $it->purchase->supplier->name }}</td>
                                        <td data-label="Bought" class="text-end">{{ $it->qty }}</td>
                                        <td data-label="Missing" class="text-end fw-bold text-danger">{{ $it->missing_qty }}</td>
                                        <td data-label="Reason">{{ $it->missing_reason }}</td>
                                        <td data-label="Value" class="text-end money">{{ money($it->missing_qty * $it->unit_cost, false) }}</td>
                                    </tr>
                                @empty
                                    <x-empty :cols="7" text="No missing or damaged pairs in this period." />
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">By supplier</h5></div>
                <ul class="list-group list-group-flush">
                    @forelse ($bySupplier as $name => $n)
                        <li class="list-group-item d-flex justify-content-between"><span>{{ $name }}</span><b class="text-danger">{{ $n }} prs</b></li>
                    @empty
                        <li class="list-group-item small text-muted">Nothing missing.</li>
                    @endforelse
                </ul>
            </div>
            <div class="small text-muted">Record missing pairs when you <b>Buy</b> ("Some pairs missing / damaged?"), or later on the purchase page with <b>Missing / damaged</b>.</div>
        </div>
    </div>
@endsection
