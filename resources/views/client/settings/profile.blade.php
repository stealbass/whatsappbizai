@extends('client.layout')
@section('title', 'Mon profil')

@section('content')
<div class="card" style="max-width:600px;">
    <div class="card-header"><h2>Mon profil</h2></div>

    @if($errors->any())
        <div class="alert alert-error">
            @foreach($errors->all() as $e) <div>• {{ $e }}</div> @endforeach
        </div>
    @endif

    <form action="{{ url('client/settings/profile') }}" method="POST">
        @csrf @method('PUT')
        <div class="form-group">
            <label>Nom *</label>
            <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required>
        </div>
        <div class="form-group">
            <label>Email *</label>
            <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required>
        </div>
        <div style="display:flex;gap:12px;margin-top:20px;">
            <button type="submit" class="btn btn-primary">Enregistrer</button>
        </div>
    </form>
</div>
@endsection
