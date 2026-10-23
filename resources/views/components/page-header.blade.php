@props(['title', 'crumbs' => []])

<div class="erp-header">
    <div>
        <h4>{{ $title }}</h4>
        @if ($crumbs)
            <div class="erp-crumbs">
                <a href="{{ route('root') }}">Home</a>
                @foreach ($crumbs as $label => $url)
                    <span class="mx-1">/</span>
                    @if (is_string($label))
                        <a href="{{ $url }}">{{ $label }}</a>
                    @else
                        <span>{{ $url }}</span>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
    @if (trim($slot))
        <div class="erp-actions">{{ $slot }}</div>
    @endif
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
        <iconify-icon icon="solar:check-circle-bold" class="fs-20"></iconify-icon>
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <div class="fw-semibold mb-1">Please fix the following:</div>
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
