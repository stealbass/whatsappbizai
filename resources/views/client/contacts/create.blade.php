@extends('client.layout')
@section('title', 'Nouveau contact')

@section('content')
<div class="card" style="max-width:600px;">
    <div class="card-header"><h2>Ajouter un contact</h2></div>

    @if($errors->any())
        <div class="alert alert-error">
            @foreach($errors->all() as $e) <div>• {{ $e }}</div> @endforeach
        </div>
    @endif

    <form action="{{ url('client/contacts') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Nom *</label>
            <input type="text" name="name" value="{{ old('name') }}" required>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Numéro WhatsApp *</label>
                <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number') }}" placeholder="+237 6XX XXX XXX" required>
            </div>
            <div class="form-group">
                <label>Téléphone</label>
                <input type="text" name="phone" value="{{ old('phone') }}">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email') }}">
            </div>
            <div class="form-group">
                <label>Entreprise</label>
                <input type="text" name="company" value="{{ old('company') }}">
            </div>
        </div>
        <div class="form-group">
            <label>Statut *</label>
            <select name="status" required>
                <option value="prospect" {{ old('status') === 'prospect' ? 'selected' : '' }}>Prospect</option>
                <option value="client" {{ old('status') === 'client' ? 'selected' : '' }}>Client</option>
                <option value="lead" {{ old('status') === 'lead' ? 'selected' : '' }}>Lead</option>
            </select>
        </div>
        <div class="form-group">
            <label>Notes</label>
            <textarea name="notes">{{ old('notes') }}</textarea>
        </div>
        <div style="display:flex;gap:12px;margin-top:20px;">
            <button type="submit" class="btn btn-primary">Créer le contact</button>
            <a href="{{ url('client/contacts') }}" class="btn btn-outline">Annuler</a>
        </div>
    </form>
</div>
@endsection
