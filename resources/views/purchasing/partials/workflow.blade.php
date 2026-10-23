@php
    $s = $po->status;
    $costed = $po->charges_total > 0 || in_array($s, ['costed', 'partial', 'received']);
    $steps = [
        ['Purchase Order', 'Order shoes from overseas supplier', in_array($s, ['ordered', 'costed', 'partial', 'received']), $s === 'draft'],
        ['Import Costing', 'Supplier cost + freight + insurance + duty + other', $costed, in_array($s, ['ordered']) && ! $costed],
        ['Goods Received (GRN)', 'Verify quantities, sizes and damaged items', in_array($s, ['partial', 'received']), $s === 'costed' || $s === 'partial'],
        ['Stock & Cost Updated', 'Inventory, landed cost and profit updated', $s === 'received', false],
    ];
@endphp
<div class="workflow mb-3">
    @foreach ($steps as $i => [$title, $text, $done, $current])
        <div class="step {{ $done ? 'done' : ($current ? 'current' : '') }}">
            <div class="step-title">
                @if ($done)<iconify-icon icon="solar:check-circle-bold" class="text-success align-middle"></iconify-icon>@else<span class="text-muted">{{ $i + 1 }}.</span>@endif
                {{ $title }}
            </div>
            <div class="text-muted">{{ $text }}</div>
        </div>
    @endforeach
</div>
