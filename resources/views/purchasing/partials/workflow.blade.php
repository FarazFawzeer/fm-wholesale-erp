@php
    $s = $po->status;
    $costed = $po->charges_total > 0 || in_array($s, ['costed', 'partial', 'received']);
    $steps = [
        ['Order', 'Order shoes from the supplier', in_array($s, ['ordered', 'costed', 'partial', 'received']), $s === 'draft'],
        ['Shipping & duty', 'Add freight, duty and other costs', $costed, in_array($s, ['ordered']) && ! $costed],
        ['Receive goods', 'Count pairs and note damaged ones', in_array($s, ['partial', 'received']), $s === 'costed' || $s === 'partial'],
        ['In stock', 'Stock and real cost updated', $s === 'received', false],
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
