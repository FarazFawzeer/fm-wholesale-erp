@extends('layouts.vertical', ['subtitle' => 'Settings'])

@section('content')
    <x-page-header title="Settings">
        <a href="{{ route('users.index') }}" class="btn btn-soft-secondary"><iconify-icon icon="solar:users-group-rounded-outline" class="align-middle me-1"></iconify-icon>Users</a>
    </x-page-header>

    <div class="row g-3">
        <div class="col-lg-6">
            <form method="POST" action="{{ route('settings.update') }}" class="card">
                @csrf
                <div class="card-header"><h5 class="card-title mb-0">Shop details (printed on invoices)</h5></div>
                <div class="card-body">
                    <div class="mb-3"><label class="form-label">Business name</label><input name="company_name" value="{{ $s['company_name'] }}" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Tagline</label><input name="company_tagline" value="{{ $s['company_tagline'] }}" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Address</label><input name="company_address" value="{{ $s['company_address'] }}" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Phone</label><input name="company_phone" value="{{ $s['company_phone'] }}" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Item No starts with</label>
                        <input name="item_prefix" value="{{ $s['item_prefix'] ?? 'ARD' }}" class="form-control" maxlength="10" style="max-width: 160px">
                        <div class="form-text">The <b>Auto</b> button makes the next number, e.g. {{ ($s['item_prefix'] ?? 'ARD') }}-1001. You can also type any item no yourself.</div></div>
                    <div class="mb-3"><label class="form-label">Note at the bottom of every invoice</label>
                        <textarea name="invoice_terms" rows="3" class="form-control" placeholder="e.g. Goods once sold can be exchanged within 7 days with the bill.">{{ $s['invoice_terms'] ?? '' }}</textarea></div>
                    <button class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>

        <div class="col-lg-6">
            @foreach ($lists as $list)
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">{{ $list['title'] }}</h5></div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('settings.items.store', $list['key']) }}" class="input-group mb-2">
                            @csrf
                            <input name="name" class="form-control" placeholder="Add new..." required>
                            <button class="btn btn-soft-primary">Add</button>
                        </form>
                        <div class="d-flex flex-wrap gap-2">
                            @forelse ($list['items'] as $item)
                                <span class="setting-item">
                                    <form method="POST" action="{{ route('settings.items.update', [$list['key'], $item->id]) }}" class="d-inline">@csrf @method('PUT')
                                        <input name="name" value="{{ $item->name }}" class="form-control form-control-sm" onchange="this.form.submit()" title="Change and press Enter to rename"></form>
                                    <small class="text-muted">{{ $item->{$list['relation'] . '_count'} }}</small>
                                    @if ($item->{$list['relation'] . '_count'} == 0)
                                        <form method="POST" action="{{ route('settings.items.destroy', [$list['key'], $item->id]) }}" class="d-inline">@csrf @method('DELETE')
                                            <button class="btn btn-link btn-sm p-0 text-danger" title="Delete"><iconify-icon icon="solar:close-circle-outline"></iconify-icon></button></form>
                                    @endif
                                </span>
                            @empty
                                <span class="text-muted small">None yet.</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
