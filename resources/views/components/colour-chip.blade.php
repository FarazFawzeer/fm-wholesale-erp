@props(['name', 'qty' => null])
<span {{ $attributes->merge(['class' => 'colour-chip']) }}>
    <i style="background: {{ \App\Models\ProductColour::hex($name) }}"></i>{{ $name }}@if (! is_null($qty))<b class="{{ $qty <= 0 ? 'text-danger' : '' }}">{{ $qty }}</b>@endif
</span>
