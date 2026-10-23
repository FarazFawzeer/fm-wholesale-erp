@props(['text' => 'Nothing here yet.', 'cols' => 1])

<tr>
    <td colspan="{{ $cols }}" class="text-center text-muted py-5">
        <iconify-icon icon="solar:box-minimalistic-outline" class="fs-32 d-block mx-auto mb-2"></iconify-icon>
        {{ $text }}
    </td>
</tr>
