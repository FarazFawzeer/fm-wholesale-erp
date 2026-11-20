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
                    <button class="btn btn-primary">Save</button>
                </div>
            </form>

            <form method="POST" action="{{ route('settings.terms') }}" class="card" data-terms>
                @csrf
                <div class="card-header d-flex justify-content-between align-items-center gap-2">
                    <div>
                        <h5 class="card-title mb-0">Terms &amp; Conditions</h5>
                        <div class="small text-muted">Printed as numbered points at the bottom of every invoice.</div>
                    </div>
                    <span class="badge bg-light text-dark js-terms-count">{{ count($terms) }}</span>
                </div>
                <div class="card-body">
                    <div class="terms-list js-terms">
                        @foreach ($terms as $t)
                            <div class="term-row">
                                <span class="term-no"></span>
                                <textarea name="terms[]" rows="1" class="form-control" maxlength="300">{{ $t }}</textarea>
                                <button type="button" class="btn btn-light term-del js-term-del" title="Delete this point"><iconify-icon icon="solar:trash-bin-minimalistic-outline"></iconify-icon></button>
                            </div>
                        @endforeach
                    </div>
                    <div class="text-muted small js-terms-empty {{ count($terms) ? 'd-none' : '' }}">No terms yet. Add the first point below.</div>
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <button type="button" class="btn btn-soft-primary js-term-add"><iconify-icon icon="solar:add-circle-outline" class="align-middle me-1"></iconify-icon>Add point</button>
                        <button class="btn btn-primary ms-auto">Save terms</button>
                    </div>
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

@push('page-js')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('[data-terms]');
    if (!form) return;
    const list = form.querySelector('.js-terms');
    const grow = (t) => { t.style.height = 'auto'; t.style.height = t.scrollHeight + 2 + 'px'; };
    const renumber = () => {
        const rows = list.querySelectorAll('.term-row');
        rows.forEach((r, i) => (r.querySelector('.term-no').textContent = i + 1));
        form.querySelector('.js-terms-count').textContent = rows.length;
        form.querySelector('.js-terms-empty').classList.toggle('d-none', rows.length > 0);
    };
    form.querySelector('.js-term-add').addEventListener('click', () => {
        const row = document.createElement('div');
        row.className = 'term-row';
        row.innerHTML = '<span class="term-no"></span><textarea name="terms[]" rows="1" class="form-control" maxlength="300" placeholder="e.g. Goods once sold cannot be returned after 7 days."></textarea>'
            + '<button type="button" class="btn btn-light term-del js-term-del" title="Delete this point"><iconify-icon icon="solar:trash-bin-minimalistic-outline"></iconify-icon></button>';
        list.appendChild(row);
        renumber();
        row.querySelector('textarea').focus();
    });
    list.addEventListener('click', (e) => {
        const del = e.target.closest('.js-term-del');
        if (del) { del.closest('.term-row').remove(); renumber(); }
    });
    list.addEventListener('input', (e) => e.target.matches('textarea') && grow(e.target));
    list.querySelectorAll('textarea').forEach(grow);
    renumber();
});
</script>
@endpush
