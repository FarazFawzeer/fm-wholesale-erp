{{-- Dropdown with a "+ New" button that switches to a text box for adding a new item on the spot --}}
<label class="form-label">{{ $label }}</label>
<div class="pick-or-add">
    <div class="input-group js-pick">
        <select name="{{ $field }}_id" class="form-select">
            <option value="">-</option>
            @foreach ($items as $item)
                <option value="{{ $item->id }}" @selected($value == $item->id)>{{ $item->name }}</option>
            @endforeach
        </select>
        <button type="button" class="btn btn-soft-primary" onclick="this.closest('.pick-or-add').classList.add('adding'); this.closest('.pick-or-add').querySelector('.js-new input').focus()">+ New</button>
    </div>
    <div class="input-group js-new">
        <input name="new_{{ $field }}" class="form-control" placeholder="New {{ strtolower($label) }} name">
        <button type="button" class="btn btn-light" onclick="const w = this.closest('.pick-or-add'); w.classList.remove('adding'); w.querySelector('.js-new input').value = ''">Cancel</button>
    </div>
</div>
