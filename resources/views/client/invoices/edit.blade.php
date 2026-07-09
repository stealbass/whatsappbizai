@extends('client.layout')
@section('title', __('app.client.invoices.edit') . ' — ' . $invoice->number)

@section('content')
<div class="card" style="max-width:700px;">
    <div class="card-header">
        <h2>{{ __('app.client.invoices.edit') }} — {{ $invoice->number }}</h2>
    </div>

    @if($errors->any())
        <div class="alert alert-error">
            @foreach($errors->all() as $e) <div>• {{ $e }}</div> @endforeach
        </div>
    @endif

    <form action="{{ url('client/invoices/' . $invoice->id) }}" method="POST" id="invoiceForm">
        @csrf
        @method('PUT')
        <div class="form-row">
            <div class="form-group">
                <label>{{ __('app.client.invoices.form.contact') }}</label>
                <select name="contact_id" required>
                    <option value="">{{ __('app.client.invoices.form.select_contact') }}</option>
                    @foreach($contacts as $c)
                        <option value="{{ $c->id }}" {{ (old('contact_id', $invoice->contact_id) == $c->id) ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>{{ __('app.client.invoices.form.issue_date') }}</label>
                <input type="date" name="issue_date" value="{{ old('issue_date', $invoice->issue_date) }}" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>{{ __('app.client.invoices.form.due_date') }}</label>
                <input type="date" name="due_date" value="{{ old('due_date', $invoice->due_date) }}">
            </div>
            <div class="form-group" style="grid-column:1/-1;">
                <label>{{ __('app.client.invoices.form.notes') }}</label>
                <textarea name="notes" id="invoice_notes">{!! old('notes', $invoice->notes) !!}</textarea>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>{{ __('app.client.invoices.form.tax_rate') }}</label>
                <input type="number" name="tax_rate" value="{{ old('tax_rate', $invoice->tax_rate) }}" min="0" max="100" step="0.01">
            </div>
            <div class="form-group">
                <label>{{ __('app.client.invoices.form.discount') }}</label>
                <input type="number" name="discount" value="{{ old('discount', $invoice->discount) }}" min="0" step="0.01">
            </div>
        </div>

        <h3 style="font-size:15px;font-weight:700;margin:20px 0 12px;">{{ __('app.client.invoices.form.lines_title') }}</h3>
        <div id="items-container">
            @foreach($invoice->items as $index => $item)
            <div class="item-row" style="display:grid;grid-template-columns:2fr 1fr 1fr auto;gap:10px;margin-bottom:10px;align-items:end;">
                <div class="form-group" style="margin:0;">
                    <label>{{ __('app.client.invoices.form.description') }}</label>
                    <input type="text" name="items[{{ $index }}][description]" value="{{ old('items.'.$index.'.description', $item->description) }}" required>
                </div>
                <div class="form-group" style="margin:0;">
                    <label>{{ __('app.client.invoices.form.quantity') }}</label>
                    <input type="number" name="items[{{ $index }}][quantity]" value="{{ old('items.'.$index.'.quantity', $item->quantity) }}" min="0.01" step="0.01" required>
                </div>
                <div class="form-group" style="margin:0;">
                    <label>{{ __('app.client.invoices.form.unit_price') }}</label>
                    <input type="number" name="items[{{ $index }}][unit_price]" value="{{ old('items.'.$index.'.unit_price', $item->unit_price) }}" step="0.01" min="0" required>
                </div>
                <button type="button" class="btn btn-danger btn-sm remove-item" style="margin-bottom:4px;">✕</button>
            </div>
            @endforeach
        </div>
        <button type="button" class="btn btn-outline btn-sm" id="addItem" style="margin-bottom:20px;">+ {{ __('app.client.invoices.form.add_line') }}</button>

        <div style="display:flex;gap:12px;margin-top:20px;">
            <button type="submit" class="btn btn-primary">{{ __('app.client.common.save') }}</button>
            <a href="{{ url('client/invoices/' . $invoice->id) }}" class="btn btn-outline">{{ __('app.client.common.cancel') }}</a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
@include('components.quill')
<script>
initQuill('#invoice_notes', 200);
let itemIndex = {{ $invoice->items->count() }};
document.getElementById('addItem').addEventListener('click', function() {
    const container = document.getElementById('items-container');
    const row = document.createElement('div');
    row.className = 'item-row';
    row.style.cssText = 'display:grid;grid-template-columns:2fr 1fr 1fr auto;gap:10px;margin-bottom:10px;align-items:end;';
    row.innerHTML = `
        <div class="form-group" style="margin:0;">
            <label>{{ __('app.client.invoices.form.description') }}</label>
            <input type="text" name="items[${itemIndex}][description]" required>
        </div>
        <div class="form-group" style="margin:0;">
            <label>{{ __('app.client.invoices.form.quantity') }}</label>
            <input type="number" name="items[${itemIndex}][quantity]" value="1" min="0.01" step="0.01" required>
        </div>
        <div class="form-group" style="margin:0;">
            <label>{{ __('app.client.invoices.form.unit_price') }}</label>
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
        if (rows.length > 1) e.target.closest('.item-row').remove();
    }
});
</script>
@endsection
