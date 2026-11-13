<form class="filter-bar mb-3 no-print" method="GET">
    <div class="period-btns" role="group">
        @foreach (['today' => 'Today', 'week' => 'This week', '' => 'This month', 'last_month' => 'Last month', 'year' => 'This year'] as $key => $label)
            <a href="{{ request()->fullUrlWithQuery(['period' => $key ?: null, 'from' => null, 'to' => null]) }}" class="btn btn-sm {{ (request('period') ?? '') === $key && ($key !== '' || ! request('from')) ? 'btn-primary' : 'btn-light' }}">{{ $label }}</a>
        @endforeach
    </div>
    <input type="date" name="from" value="{{ $from }}" class="form-control form-control-sm" title="From">
    <input type="date" name="to" value="{{ $to }}" class="form-control form-control-sm" title="To">
    <button class="btn btn-sm btn-soft-primary">Show</button>
</form>
