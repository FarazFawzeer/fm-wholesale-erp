@props(['product', 'size' => 44])
@if ($product?->image_path)
    <img src="{{ asset($product->image_path) }}" alt="" class="thumb" style="width: {{ $size }}px; height: {{ $size }}px" loading="lazy">
@else
    <span class="thumb thumb-empty" style="width: {{ $size }}px; height: {{ $size }}px"><iconify-icon icon="ph:sneaker"></iconify-icon></span>
@endif
