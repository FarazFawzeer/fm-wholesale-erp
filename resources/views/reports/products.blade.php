@extends('layouts.vertical', ['subtitle' => 'Best sellers'])

@section('content')
    <x-page-header title="Reports">
        <button type="button" class="btn btn-soft-secondary" onclick="window.print()"><iconify-icon icon="solar:printer-outline" class="align-middle me-1"></iconify-icon>Print</button>
    </x-page-header>
    @include('reports.tabs')
    @include('reports.partials.range')

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between"><h5 class="card-title mb-0">Best selling products</h5><span class="small text-muted">{{ qty($rows->sum('pairs')) }} pairs</span></div>
                <div class="card-body table-wrap p-0">
                    <div class="table-responsive">
                        <table class="table erp-table stack mb-0">
                            <thead><tr><th>#</th><th>Product</th><th class="text-end">Pairs</th><th class="text-end">Sales</th><th class="text-end">Profit</th></tr></thead>
                            <tbody>
                                @forelse ($rows as $r)
                                    <tr>
                                        <td class="d-none d-md-table-cell text-muted">{{ $loop->iteration }}</td>
                                        <td class="td-title"><a href="{{ route('products.show', $r->id) }}" class="d-flex align-items-center gap-2">
                                            @if ($r->image_path)<img src="{{ asset($r->image_path) }}" class="thumb" style="width: 36px; height: 36px" alt="">@else<span class="thumb thumb-empty" style="width: 36px; height: 36px"><iconify-icon icon="ph:sneaker"></iconify-icon></span>@endif
                                            <span><span class="fw-semibold">{{ $r->name }}</span> <small class="text-muted">{{ $r->code }}</small></span></a></td>
                                        <td data-label="Pairs" class="text-end fw-semibold">{{ qty($r->pairs) }}</td>
                                        <td data-label="Sales" class="text-end money">{{ money($r->amount, false) }}</td>
                                        <td data-label="Profit" class="text-end money {{ $r->profit < 0 ? 'text-danger' : 'text-success' }}">{{ money($r->profit, false) }}</td>
                                    </tr>
                                @empty
                                    <x-empty :cols="5" text="No sales in this period." />
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Best selling colours</h5></div>
                <ul class="list-group list-group-flush">
                    @php $max = max(1, (int) $colours->max('pairs')); @endphp
                    @forelse ($colours as $c)
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between mb-1"><x-colour-chip :name="$c->colour" /><strong>{{ qty($c->pairs) }}</strong></div>
                            <div class="progress" style="height: 6px"><div class="progress-bar" style="width: {{ round($c->pairs / $max * 100) }}%"></div></div>
                        </li>
                    @empty
                        <li class="list-group-item text-muted small">No sales in this period.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@endsection
