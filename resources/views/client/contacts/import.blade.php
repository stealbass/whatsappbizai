@extends('client.layout')
@section('title', __('app.client.contacts.import_title'))

@section('content')
<div class="card" style="max-width:720px;">
    <div class="card-header">
        <h2>{{ __('app.client.contacts.import_title') }}</h2>
    </div>

    <p style="color:var(--muted);margin-bottom:20px;">{{ __('app.client.contacts.import_desc') }}</p>

    <div style="background:var(--bg);border:1px solid var(--border);border-radius:10px;padding:20px;margin-bottom:24px;">
        <h3 style="margin:0 0 8px;font-size:15px;">{{ __('app.client.contacts.import_expected') }}</h3>
        <div style="overflow-x:auto;">
            <table style="width:100%;font-size:13px;border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:1px solid var(--border);">
                        <th style="text-align:left;padding:6px 8px;">name</th>
                        <th style="text-align:left;padding:6px 8px;">whatsapp_number</th>
                        <th style="text-align:left;padding:6px 8px;">email</th>
                        <th style="text-align:left;padding:6px 8px;">company</th>
                        <th style="text-align:left;padding:6px 8px;">status</th>
                        <th style="text-align:left;padding:6px 8px;">notes</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding:6px 8px;">Jean Dupont</td>
                        <td style="padding:6px 8px;">+237600000001</td>
                        <td style="padding:6px 8px;">jean@example.com</td>
                        <td style="padding:6px 8px;">Dupont SARL</td>
                        <td style="padding:6px 8px;">client</td>
                        <td style="padding:6px 8px;">Client depuis 2023</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p style="color:var(--muted);font-size:12px;margin:10px 0 0;">
            {{ __('app.client.contacts.import_notes') }}
        </p>
    </div>

    <div style="display:flex;gap:12px;margin-bottom:20px;">
        <a href="{{ url('client/contacts/import/template') }}" class="btn btn-ghost" style="display:inline-flex;align-items:center;gap:6px;">
            📥 {{ __('app.client.contacts.import_download_template') }}
        </a>
    </div>

    <form action="{{ url('client/contacts/import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div style="margin-bottom:20px;">
            <label for="csv_file" style="display:block;font-weight:600;margin-bottom:6px;">{{ __('app.client.contacts.import_file_label') }}</label>
            <input type="file" id="csv_file" name="csv_file" accept=".csv,.txt" required
                   style="display:block;width:100%;padding:10px;border:1px solid var(--border);border-radius:8px;background:var(--bg);color:var(--text);font-size:14px;">
            @error('csv_file')
                <p style="color:var(--red);font-size:13px;margin-top:4px;">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">
            📤 {{ __('app.client.contacts.import_submit') }}
        </button>
        <a href="{{ url('client/contacts') }}" class="btn btn-ghost" style="margin-left:8px;">{{ __('app.client.contacts.import_cancel') }}</a>
    </form>
</div>
@endsection
