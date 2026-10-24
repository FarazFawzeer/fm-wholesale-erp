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
                    <p class="text-muted">Fill one row per product. Size and colour are optional. New brands and categories are added automatically. Leave the code empty to get one automatically; an existing code updates that product.</p>
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
                            @foreach (['code' => 'Product code (optional - automatic if empty)', 'name' => 'Product name (required)', 'brand' => 'Brand name', 'category' => 'Category name', 'colour' => 'Colour', 'size_from' => 'Smallest size, e.g. 28', 'size_to' => 'Largest size, e.g. 44', 'selling_price' => 'Selling price per pair (Rs)', 'min_price' => 'Lowest price allowed (optional)', 'reorder_level' => 'Warn when stock is below (pairs)'] as $col => $desc)
                                <tr><td><code>{{ $col }}</code></td><td class="text-muted">{{ $desc }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
