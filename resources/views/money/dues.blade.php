@extends('layouts.vertical', ['subtitle' => 'Dues'])

@section('content')
    <x-page-header title="Dues - who has not paid" />

    <div class="row g-3 mb-3">
        <div class="col-6"><a href="{{ route('dues', ['tab' => 'customers']) }}" class="d-block"><x-kpi label="Customers owe us" :value="money($customerTotal)" icon="solar:hand-money-outline" color="danger" /></a></div>
        <div class="col-6"><a href="{{ route('dues', ['tab' => 'suppliers']) }}" class="d-block"><x-kpi label="We owe suppliers" :value="money($supplierTotal)" icon="solar:card-send-outline" color="warning" /></a></div>
    </div>

    <ul class="nav nav-pills mb-3">
        <li class="nav-item"><a class="nav-link {{ $tab === 'customers' ? 'active' : '' }}" href="{{ route('dues', ['tab' => 'customers']) }}">Customers</a></li>
        <li class="nav-item"><a class="nav-link {{ $tab === 'suppliers' ? 'active' : '' }}" href="{{ route('dues', ['tab' => 'suppliers']) }}">Suppliers</a></li>
    </ul>

    @if ($rows->isEmpty())
        <div class="card"><div class="card-body text-center text-muted py-5">
            <iconify-icon icon="solar:check-circle-outline" class="fs-48 d-block mx-auto mb-2 text-success"></iconify-icon>
            {{ $tab === 'customers' ? 'Every customer has paid.' : 'You do not owe any supplier.' }}
        </div></div>
    @else
        <div class="due-list">
            @foreach ($rows as $r)
                @php
                    $p = $r['party'];
                    $level = $r['days'] > 60 ? 'danger' : ($r['days'] > 30 ? 'warning' : 'secondary');
                @endphp
                <div class="card due-card mb-2">
                    <div class="card-body">
                        <div class="min-w-0 flex-grow-1">
                            <a href="{{ $tab === 'customers' ? route('customers.show', $p) : route('suppliers.show', $p) }}" class="fw-bold fs-6 d-block text-truncate">{{ $p->name }}</a>
                            <div class="small text-muted">
                                @if ($tab === 'customers' && $p->city){{ $p->city }} &middot; @endif
                                {{ $r['bills'] }} unpaid {{ Str::plural('bill', $r['bills']) }} &middot; since {{ $r['since']->format('d M Y') }}
                                <span class="badge bg-{{ $level }}-subtle text-{{ $level }} ms-1">{{ $r['days'] === 0 ? 'today' : $r['days'] . ' days' }}</span>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold fs-5 money {{ $tab === 'customers' ? 'text-danger' : 'text-warning' }}">{{ money($r['due']) }}</div>
                            <div class="d-flex gap-1 justify-content-end mt-1">
                                @if ($tab === 'customers')
                                    @if ($wa = $p->whatsappNumber())
                                        <a href="https://wa.me/{{ $wa }}?text={{ rawurlencode('Dear ' . $p->name . ', this is a friendly reminder from ' . setting('company_name') . '. Your balance to pay is ' . money($r['due']) . '. Thank you!') }}" target="_blank" rel="noopener" class="btn btn-sm btn-soft-success" title="WhatsApp reminder"><i class="bx bxl-whatsapp fs-16"></i></a>
                                    @endif
                                    @if ($p->phone)<a href="tel:{{ $p->phone }}" class="btn btn-sm btn-soft-secondary" title="Call"><iconify-icon icon="solar:phone-outline"></iconify-icon></a>@endif
                                    <a href="{{ route('payments.receive', ['customer' => $p->id]) }}" class="btn btn-sm btn-success">Get paid</a>
                                @else
                                    <a href="{{ route('payments.pay', ['supplier' => $p->id]) }}" class="btn btn-sm btn-warning">Pay</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
