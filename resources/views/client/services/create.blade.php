@extends('client.layout')
@section('title', 'Nouveau service')

@section('content')
<div class="card" style="max-width:600px;">
    <div class="card-header"><h2>Ajouter un service</h2></div>

    @if($errors->any())
        <div class="alert alert-error">
            @foreach($errors->all() as $e) <div>• {{ $e }}</div> @endforeach
        </div>
    @endif

    <form action="{{ url('client/services') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Nom *</label>
            <input type="text" name="name" value="{{ old('name') }}" required>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description">{{ old('description') }}</textarea>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Prix unitaire *</label>
                <input type="number" name="unit_price" value="{{ old('unit_price') }}" step="0.01" min="0" required>
            </div>
            <div class="form-group">
                <label>Unité *</label>
                <select name="unit" required>
                    <option value="forfait" {{ old('unit') === 'forfait' ? 'selected' : '' }}>Forfait</option>
                    <option value="heure" {{ old('unit') === 'heure' ? 'selected' : '' }}>Heure</option>
                    <option value="mois" {{ old('unit') === 'mois' ? 'selected' : '' }}>Mois</option>
                    <option value="unité" {{ old('unit') === 'unité' ? 'selected' : '' }}>Unité</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label style="display:flex;align-items:center;gap:8px;">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                Service actif
            </label>
        </div>
        <div style="display:flex;gap:12px;margin-top:20px;">
            <button type="submit" class="btn btn-primary">Créer le service</button>
            <a href="{{ url('client/services') }}" class="btn btn-outline">Annuler</a>
        </div>
    </form>
</div>
@endsection
