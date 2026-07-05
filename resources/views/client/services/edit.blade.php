@extends('client.layout')
@section('title', 'Modifier service')

@section('content')
<div class="card" style="max-width:600px;">
    <div class="card-header"><h2>Modifier {{ $service->name }}</h2></div>

    @if($errors->any())
        <div class="alert alert-error">
            @foreach($errors->all() as $e) <div>• {{ $e }}</div> @endforeach
        </div>
    @endif

    <form action="{{ url('client/services/' . $service->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="form-group">
            <label>Nom *</label>
            <input type="text" name="name" value="{{ old('name', $service->name) }}" required>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description">{{ old('description', $service->description) }}</textarea>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Prix unitaire *</label>
                <input type="number" name="unit_price" value="{{ old('unit_price', $service->unit_price) }}" step="0.01" min="0" required>
            </div>
            <div class="form-group">
                <label>Unité *</label>
                <select name="unit" required>
                    <option value="forfait" {{ old('unit', $service->unit) === 'forfait' ? 'selected' : '' }}>Forfait</option>
                    <option value="heure" {{ old('unit', $service->unit) === 'heure' ? 'selected' : '' }}>Heure</option>
                    <option value="mois" {{ old('unit', $service->unit) === 'mois' ? 'selected' : '' }}>Mois</option>
                    <option value="unité" {{ old('unit', $service->unit) === 'unité' ? 'selected' : '' }}>Unité</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label style="display:flex;align-items:center;gap:8px;">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $service->is_active) ? 'checked' : '' }}>
                Service actif
            </label>
        </div>
        <div style="display:flex;gap:12px;margin-top:20px;">
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="{{ url('client/services') }}" class="btn btn-outline">Annuler</a>
        </div>
    </form>
</div>
@endsection
