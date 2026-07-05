@extends('client.layout')
@section('title', __('app.client.services.new'))

@section('content')
<div class="card" style="max-width:600px;">
    <div class="card-header"><h2>{{ __('app.client.services.create') }}</h2></div>

    @if($errors->any())
        <div class="alert alert-error">
            @foreach($errors->all() as $e) <div>• {{ $e }}</div> @endforeach
        </div>
    @endif

    <form action="{{ url('client/services') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>{{ __('app.client.services.name') }} *</label>
            <input type="text" name="name" value="{{ old('name') }}" required>
        </div>
        <div class="form-group">
            <label>{{ __('app.client.services.description') }}</label>
            <textarea name="description">{{ old('description') }}</textarea>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>{{ __('app.client.services.price') }} *</label>
                <input type="number" name="unit_price" value="{{ old('unit_price') }}" step="0.01" min="0" required>
            </div>
            <div class="form-group">
                <label>{{ __('app.client.services.unit') }} *</label>
                <select name="unit" required>
                    <option value="forfait" {{ old('unit') === 'forfait' ? 'selected' : '' }}>{{ __('app.client.service_units.forfait') }}</option>
                    <option value="heure" {{ old('unit') === 'heure' ? 'selected' : '' }}>{{ __('app.client.service_units.heure') }}</option>
                    <option value="mois" {{ old('unit') === 'mois' ? 'selected' : '' }}>{{ __('app.client.service_units.mois') }}</option>
                    <option value="unité" {{ old('unit') === 'unité' ? 'selected' : '' }}>{{ __('app.client.service_units.unite') }}</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label style="display:flex;align-items:center;gap:8px;">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                {{ __('app.client.services.active') }}
            </label>
        </div>
        <div style="display:flex;gap:12px;margin-top:20px;">
            <button type="submit" class="btn btn-primary">{{ __('app.client.services.create') }}</button>
            <a href="{{ url('client/services') }}" class="btn btn-outline">{{ __('app.client.common.cancel') }}</a>
        </div>
    </form>
</div>
@endsection
