@extends('client.layout')
@section('title', 'Changer le mot de passe')

@section('content')
<div class="card" style="max-width:600px;">
    <div class="card-header"><h2>Changer le mot de passe</h2></div>

    @if($errors->any())
        <div class="alert alert-error">
            @foreach($errors->all() as $e) <div>• {{ $e }}</div> @endforeach
        </div>
    @endif

    <form action="{{ url('client/settings/password') }}" method="POST">
        @csrf @method('PUT')
        <div class="form-group">
            <label>Mot de passe actuel *</label>
            <input type="password" name="current_password" required>
        </div>
        <div class="form-group">
            <label>Nouveau mot de passe *</label>
            <input type="password" name="password" required>
        </div>
        <div class="form-group">
            <label>Confirmer le mot de passe *</label>
            <input type="password" name="password_confirmation" required>
        </div>
        <div style="display:flex;gap:12px;margin-top:20px;">
            <button type="submit" class="btn btn-primary">Mettre à jour</button>
            <a href="{{ url('client/settings/profile') }}" class="btn btn-outline">Annuler</a>
        </div>
    </form>
</div>
@endsection
