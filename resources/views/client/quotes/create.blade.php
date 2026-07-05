@extends('client.layout')
@section('title', __('app.client.quotes.new'))

@section('content')
<div class="card" style="max-width:700px;">
    <div class="card-header"><h2>{{ __('app.client.quotes.create') }}</h2></div>

    @if($errors->any())
        <div class="alert alert-error">
            @foreach($errors->all() as $e) <div>• {{ $e }}</div> @endforeach
        </div>
    @endif

    <form action="{{ url('client/quotes') }}" method="POST" id="quoteForm">
        @csrf
        <div class="form-row">
            <div class="form-group">
                <label>{{ __('app.client.quotes.form.contact') }}</label>
                <select name="contact_id" required>
                    <option value="">-- Sélectionner un contact --</option>
                    @foreach($contacts as $c)
                        <option value="{{ $c->id }}" {{ old('contact_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>{{ __('app.client.quotes.form.valid_until') }}</label>
                <input type="date" name="valid_until" value="{{ old('valid_until') }}" required>
            </div>
        </div>
        <div class="form-group">
            <label>{{ __('app.client.quotes.form.notes') }}</label>
            <textarea name="notes">{{ old('notes') }}</textarea>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>{{ __('app.client.quotes.form.tax_rate') }}</label>
                <input type="number" name="tax_rate" value="{{ old('tax_rate', 0) }}" min="0" max="100" step="0.1">
            </div>
            <div class="form-group">
                <label>{{ __('app.client.quotes.form.discount') }}</label>
                <input type="number" name="discount" value="{{ old('discount', 0) }}" min="0" step="0.01">
            </div>
        </div>

        <h3 style="font-size:15px;font-weight:700;margin:20px 0 12px;">Lignes du devis</h3>
        <div id="items-container">
            <div class="item-row" style="display:grid;grid-template-columns:2fr 1fr 1fr auto;gap:10px;margin-bottom:10px;align-items:end;">
                <div class="form-group" style="margin:0;">
                    <label>{{ __('app.client.quotes.form.description') }}</label>
                    <input type="text" name="items[0][description]" value="{{ old('items.0.description') }}" required>
                </div>
                <div class="form-group" style="margin:0;">
                    <label>{{ __('app.client.quotes.form.quantity') }}</label>
                    <input type="number" name="items[0][quantity]" value="{{ old('items.0.quantity', 1) }}" min="1" required>
                </div>
                <div class="form-group" style="margin:0;">
                    <label>{{ __('app.client.quotes.form.unit_price') }}</label>
                    <input type="number" name="items[0][unit_price]" value="{{ old('items.0.unit_price') }}" step="0.01" min="0" required>
                </div>
                <button type="button" class="btn btn-danger btn-sm remove-item" style="margin-bottom:4px;">✕</button>
            </div>
        </div>
        <button type="button" class="btn btn-outline btn-sm" id="addItem" style="margin-bottom:20px;">+ {{ __('app.client.quotes.form.add_line') }}</button>

        <div style="display:flex;gap:12px;margin-top:20px;">
            <button type="submit" class="btn btn-primary">{{ __('app.client.quotes.form.submit') }}</button>
            <a href="{{ url('client/quotes') }}" class="btn btn-outline">{{ __('app.client.quotes.form.cancel') }}</a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
let itemIndex = 1;
document.getElementById('addItem').addEventListener('click', function() {
    const container = document.getElementById('items-container');
    const row = document.createElement('div');
    row.className = 'item-row';
    row.style.cssText = 'display:grid;grid-template-columns:2fr 1fr 1fr auto;gap:10px;margin-bottom:10px;align-items:end;';
    row.innerHTML = `
        <div class="form-group" style="margin:0;">
            <label>{{ __('app.client.quotes.form.description') }}</label>
            <input type="text" name="items[${itemIndex}][description]" required>
        </div>
        <div class="form-group" style="margin:0;">
            <label>{{ __('app.client.quotes.form.quantity') }}</label>
            <input type="number" name="items[${itemIndex}][quantity]" value="1" min="1" required>
        </div>
        <div class="form-group" style="margin:0;">
            <label>{{ __('app.client.quotes.form.unit_price') }}</label>
            <input type="number" name="items[${itemIndex}][unit_price]" step="0.01" min="0" required>
        </div>
        <button type="button" class="btn btn-danger btn-sm remove-item" style="margin-bottom:4px;">✕</button>
    `;
    container.appendChild(row);
    itemIndex++;
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-item')) {
        const rows = document.querySelectorAll('.item-row');
        if (rows.length > 1) {
            e.target.closest('.item-row').remove();
        }
    }
});
</script>
@endsection
