@extends('client.layout')
@section('title', __('app.client.settings.profile.password_title'))

@section('content')
<div class="card" style="max-width:600px;">
    <div class="card-header"><h2>{{ __('app.client.settings.profile.password_title') }}</h2></div>

    @if($errors->any())
        <div class="alert alert-error">
            @foreach($errors->all() as $e) <div>• {{ $e }}</div> @endforeach
        </div>
    @endif

    <form action="{{ url('client/settings/password') }}" method="POST">
        @csrf @method('PUT')
        <div class="form-group">
            <label>{{ __('app.client.settings.profile.current_password') }} *</label>
            <input type="password" name="current_password" required>
        </div>
        <div class="form-group">
            <label>{{ __('app.client.settings.profile.new_password') }} *</label>
            <input type="password" name="password" required>
        </div>
        <div class="form-group">
            <label>{{ __('app.client.settings.profile.confirm_password') }} *</label>
            <input type="password" name="password_confirmation" required>
        </div>
        <div style="display:flex;gap:12px;margin-top:20px;">
            <button type="submit" class="btn btn-primary">Mettre à jour</button>
            <a href="{{ url('client/settings/profile') }}" class="btn btn-outline">Annuler</a>
        </div>
    </form>
</div>
@endsection
