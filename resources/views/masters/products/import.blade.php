@extends('layouts.vertical', ['subtitle' => 'Bulk Product Import'])

@section('content')
    <x-page-header title="Bulk Product Import" :crumbs="['Products' => route('products.index'), 'Import']" />

    @if (session('import_errors'))
        <div class="alert alert-warning">
            <div class="fw-semibold">Some rows were skipped:</div>
            <ul class="mb-0">@foreach (session('import_errors') as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">1. Download the template</h5>
                    <p class="text-muted">Fill one row per model and colour. Sizes can be a range (<code>39-44</code>) or a list (<code>36|37|38</code>). Brands and categories that don't exist yet are created automatically. Existing product codes are updated.</p>
                    <a href="{{ route('products.template') }}" class="btn btn-soft-primary mb-4"><iconify-icon icon="solar:download-outline" class="align-middle me-1"></iconify-icon>Download CSV template</a>

                    <h5 class="mb-3">2. Upload the file</h5>
                    <form method="POST" action="{{ route('products.import.store') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="file" accept=".csv,text/csv" class="form-control mb-3" required>
                        <div class="form-text mb-3">In Excel use <strong>File &rarr; Save As &rarr; CSV (Comma delimited)</strong>.</div>
                        <button class="btn btn-primary">Import products</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Columns</h5></div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <tbody>
                            @foreach (['code' => 'Article / product code (required, unique)', 'name' => 'Model name (required)', 'brand' => 'Brand name', 'category' => 'Category name', 'colour' => 'Colour', 'gender' => 'Men / Ladies / Kids / Unisex', 'sizes' => '39-44 or 36|37|38 (required)', 'pairs_per_carton' => 'Default 12', 'selling_price' => 'Wholesale price per pair (Rs)', 'min_price' => 'Optional minimum price', 'reorder_level' => 'Low-stock alert level in pairs'] as $col => $desc)
                                <tr><td><code>{{ $col }}</code></td><td class="text-muted">{{ $desc }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
