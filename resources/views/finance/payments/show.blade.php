<x-print-doc :title="$payment->direction === 'in' ? 'RECEIPT' : 'PAYMENT VOUCHER'" :back="route($payment->direction === 'in' ? 'receipts.index' : 'supplier-payments.index')">
    <x-slot:meta>
        <div class="b">{{ $payment->number }}</div>
        <div class="muted">Date: {{ $payment->date->format('d M Y') }}</div>
    </x-slot:meta>

    @if (session('success'))<p style="background:#ecfdf5;color:#047857;padding:8px 10px;border-radius:6px">{{ session('success') }}</p>@endif

    <div class="parties">
        <div>
            <div class="muted">{{ $payment->direction === 'in' ? 'Received from' : 'Paid to' }}</div>
            <div class="b">{{ $party?->name }}</div>
            <div>{{ $party?->address }}</div>
        </div>
        <div class="r"><div class="muted">Amount</div><div class="b" style="font-size:20px">{{ money($payment->total) }}</div></div>
    </div>

    <table>
        <thead><tr><th>Method</th><th>Details</th><th class="r">Amount</th></tr></thead>
        <tbody>
            @foreach ($payment->lines as $l)
                <tr>
                    <td>{{ \App\Models\Payment::METHODS[$l->method] ?? $l->method }}</td>
                    <td>
                        @if ($l->cheque)
                            Cheque #{{ $l->cheque->cheque_no }} {{ $l->cheque->bank }} dated {{ $l->cheque->cheque_date->format('d M Y') }}
                            <span class="muted">({{ ucfirst($l->cheque->status) }})</span>
                        @else
                            {{ $l->account?->name }} {{ $l->reference ? '· ' . $l->reference : '' }}
                        @endif
                    </td>
                    <td class="r">{{ money($l->amount, false) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if ($payment->allocations->count())
        <h4 style="margin:18px 0 6px">Applied to</h4>
        <table>
            <tbody>
                @foreach ($payment->allocations as $a)
                    <tr><td>{{ $a->invoice?->number ?? $a->purchaseOrder?->number }}</td><td class="r">{{ money($a->amount, false) }}</td></tr>
                @endforeach
            </tbody>
        </table>
    @endif
    @if ($payment->notes)<p class="muted">{!! nl2br(e($payment->notes)) !!}</p>@endif
    <div class="foot"><div class="sign">{{ $payment->user?->name }}</div><div class="sign">Signature</div></div>
</x-print-doc>
