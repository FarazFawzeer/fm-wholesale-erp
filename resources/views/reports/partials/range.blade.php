<div class="card no-print">
    <div class="card-body">
        <form class="filter-bar">
            <div><label class="form-label small mb-1">From</label><input type="date" name="from" value="{{ $from }}" class="form-control"></div>
            <div><label class="form-label small mb-1">To</label><input type="date" name="to" value="{{ $to }}" class="form-control"></div>
            {{ $slot ?? '' }}
            <button class="btn btn-light">Show</button>
            <div class="btn-group">
                @foreach (['This month' => [now()->startOfMonth(), now()], 'Last 30 days' => [now()->subDays(30), now()], 'Last month' => [now()->subMonthNoOverflow()->startOfMonth(), now()->subMonthNoOverflow()->endOfMonth()]] as $l => [$a, $b])
                    <a class="btn btn-sm btn-outline-secondary" href="?{{ http_build_query(array_merge(request()->except(['from', 'to']), ['from' => $a->toDateString(), 'to' => $b->toDateString()])) }}">{{ $l }}</a>
                @endforeach
            </div>
            <button type="button" class="btn btn-light ms-auto" onclick="window.print()"><iconify-icon icon="solar:printer-outline" class="align-middle"></iconify-icon></button>
        </form>
    </div>
</div>
