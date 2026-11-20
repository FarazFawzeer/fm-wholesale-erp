{{--
    Split payment: cash + card + online + cheque in any mix.
    $step (number), $title, $hint, $target (fixed amount when there is no item editor)
--}}
<div class="card step-card" data-payment-lines data-target="{{ $target ?? '' }}" data-initial='@json(old('payments') ? array_values(old('payments')) : [])' data-start-empty="{{ $startEmpty ?? '0' }}">
    <div class="card-header">
        @isset($step)<span class="step-no">{{ $step }}</span>@endisset
        <h5 class="card-title mb-0">{{ $title ?? 'Payment' }}</h5>
    </div>
    <div class="card-body">
        @if (! empty($payLater))
            @php $later = ! old('payments'); @endphp
            <div class="pay-when mb-2" role="radiogroup">
                <label class="{{ $later ? 'active' : '' }}"><input type="radio" name="pay_when" value="later" class="js-pay-when" @checked($later)>
                    <iconify-icon icon="solar:clock-circle-outline"></iconify-icon><span><b>Pay later</b><small>Send invoice, get paid later</small></span></label>
                <label class="{{ $later ? '' : 'active' }}"><input type="radio" name="pay_when" value="now" class="js-pay-when" @checked(! $later)>
                    <iconify-icon icon="solar:wad-of-money-outline"></iconify-icon><span><b>Paid now</b><small>Cash, card, online or cheque</small></span></label>
            </div>
        @endif
        <div class="pay-now-box">
        @isset($hint)<p class="text-muted small mb-2">{{ $hint }}</p>@endisset
        <div class="method-picker mb-2">
            @foreach (\App\Models\Payment::METHODS as $key => $label)
                <button type="button" class="method-btn" data-add-method="{{ $key }}"><iconify-icon icon="{{ \App\Models\Payment::ICONS[$key] }}"></iconify-icon><span>{{ $label }}</span></button>
            @endforeach
        </div>
        <div class="pay-lines"></div>
        <div class="pay-summary">
            <div><span class="text-muted">Paying now</span><strong class="money">Rs <span class="js-paid">0.00</span></strong></div>
            <div class="js-balance-row"><span class="text-muted">{{ $balanceLabel ?? 'Balance (to pay later)' }}</span><strong class="money text-danger">Rs <span class="js-balance">0.00</span></strong></div>
        </div>
        <div class="d-flex gap-2 mt-2 flex-wrap">
            @foreach ($quick ?? [] as $label => $amount)
                <button type="button" class="btn btn-sm btn-soft-success" data-pay-amount="{{ $amount }}">{{ $label }}</button>
            @endforeach
            <button type="button" class="btn btn-sm btn-soft-success js-pay-full">{{ $fullLabel ?? 'Paid in full (cash)' }}</button>
            <button type="button" class="btn btn-sm btn-soft-secondary js-pay-none {{ ! empty($payLater) || isset($target) ? 'd-none' : '' }}">{{ isset($target) ? 'Clear' : 'Not paid now' }}</button>
        </div>
        </div>
    </div>
</div>
