@extends('client.layout')
@section('title', __('app.client.contacts.edit'))

@section('content')
<div class="card" style="max-width:600px;">
    <div class="card-header"><h2>{{ __('app.client.contacts.edit') }} {{ $contact->name }}</h2></div>

    @if($errors->any())
        <div class="alert alert-error">
            @foreach($errors->all() as $e) <div>• {{ $e }}</div> @endforeach
        </div>
    @endif

    <form action="{{ url('client/contacts/' . $contact->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="form-group">
            <label>{{ __('app.client.contacts.name') }} *</label>
            <input type="text" name="name" value="{{ old('name', $contact->name) }}" required>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>{{ __('app.client.contacts.whatsapp') }} *</label>
                <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $contact->whatsapp_number) }}" required>
            </div>
            <div class="form-group">
                <label>{{ __('app.client.contacts.phone') }}</label>
                <input type="text" name="phone" value="{{ old('phone', $contact->phone) }}">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>{{ __('app.client.contacts.email') }}</label>
                <input type="email" name="email" value="{{ old('email', $contact->email) }}">
            </div>
            <div class="form-group">
                <label>{{ __('app.client.contacts.company') }}</label>
                <input type="text" name="company" value="{{ old('company', $contact->company) }}">
            </div>
        </div>
        <div class="form-group">
            <label>{{ __('app.client.contacts.status') }} *</label>
            <select name="status" required>
                <option value="prospect" {{ old('status', $contact->status) === 'prospect' ? 'selected' : '' }}>{{ __('app.client.contacts.prospect') }}</option>
                <option value="client" {{ old('status', $contact->status) === 'client' ? 'selected' : '' }}>{{ __('app.client.contacts.client') }}</option>
                <option value="inactif" {{ old('status', $contact->status) === 'inactif' ? 'selected' : '' }}>{{ __('app.client.contacts.inactive') }}</option>
            </select>
        </div>
        <div class="form-group">
            <label>{{ __('app.client.contacts.notes') }}</label>
            <textarea name="notes" id="contact_notes">{{ old('notes', $contact->notes) }}</textarea>
        </div>
        <div style="display:flex;gap:12px;margin-top:20px;">
            <button type="submit" class="btn btn-primary">{{ __('app.client.common.save') }}</button>
            <a href="{{ url('client/contacts') }}" class="btn btn-outline">{{ __('app.client.common.cancel') }}</a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
@include('components.tinymce')
<script>initTinyMCE('#contact_notes', 220);</script>
@endsection
