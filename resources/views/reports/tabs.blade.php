<ul class="nav nav-pills nav-tabs-scroll mb-3 no-print">
    @foreach ([['Money & profit', 'reports.index', 'solar:chart-square-outline'], ['Best sellers', 'reports.products', 'solar:cup-star-outline'], ['Stock', 'reports.stock', 'solar:box-outline'], ['Dues', 'dues', 'solar:bell-bing-outline']] as [$label, $route, $icon])
        <li class="nav-item"><a class="nav-link {{ request()->routeIs($route) ? 'active' : '' }}" href="{{ route($route) }}"><iconify-icon icon="{{ $icon }}" class="align-middle me-1"></iconify-icon>{{ $label }}</a></li>
    @endforeach
</ul>
