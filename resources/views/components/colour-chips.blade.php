{{-- Colour chips grouped by colour: "● Blue  5-7 10  11-1 12" (each colour once, its size ranges inside). --}}
@props(['lines', 'field' => 'qty', 'qty' => true])
@foreach (colour_groups($lines, $field) as $g)
    <span {{ $attributes->merge(['class' => 'colour-chip' . ($g['sized'] ? ' colour-chip-sizes' : '')]) }}>
        <i style="background: {{ $g['hex'] }}"></i>{{ $g['colour'] }}
        @if (! $g['sized'])
            @if ($qty)<b class="{{ $g['qty'] <= 0 ? 'text-danger' : '' }}">{{ $g['qty'] }}</b>@endif
        @else
            @foreach ($g['sizes'] as $s)<span class="cc-size">{{ $s['size'] !== '' ? $s['size'] : 'other' }}@if ($qty)<b class="{{ $s['qty'] <= 0 ? 'text-danger' : '' }}">{{ $s['qty'] }}</b>@endif</span>@endforeach
        @endif
    </span>
@endforeach
