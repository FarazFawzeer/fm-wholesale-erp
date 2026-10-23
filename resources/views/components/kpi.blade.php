@props(['label', 'value', 'icon' => 'solar:chart-2-outline', 'color' => 'primary', 'sub' => null, 'href' => null])

<div class="card kpi-card h-100 mb-0">
    @if ($href)<a href="{{ $href }}" class="stretched-link" aria-label="{{ $label }}"></a>@endif
    <div class="card-body">
        <div class="kpi-icon bg-{{ $color }}-subtle text-{{ $color }}">
            <iconify-icon icon="{{ $icon }}"></iconify-icon>
        </div>
        <div class="min-w-0">
            <div class="kpi-label">{{ $label }}</div>
            <div class="kpi-value">{{ $value }}</div>
            @if ($sub)<div class="kpi-sub">{!! $sub !!}</div>@endif
        </div>
    </div>
</div>
