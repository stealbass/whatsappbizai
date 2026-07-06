@extends('client.layout')
@section('title', __('app.client.settings.whatsapp.title'))

@section('content')
<div class="card" style="max-width:700px;">
    <div class="card-header"><h2>{{ __('app.client.settings.whatsapp.title') }}</h2></div>

    @if($errors->any())
        <div class="alert alert-error">
            @foreach($errors->all() as $e) <div>• {{ $e }}</div> @endforeach
        </div>
    @endif

    <form action="{{ url('client/settings/whatsapp') }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')

        <div class="form-group">
            <label>{{ __('app.client.settings.whatsapp.phone_id_label') }}</label>
            <input type="text" name="whatsapp_phone_number_id" value="{{ old('whatsapp_phone_number_id', $business->whatsapp_phone_number_id ?? '') }}">
            <p class="form-help">{{ __('app.client.settings.whatsapp.phone_id_help') }}</p>
        </div>
        <div class="form-group">
            <label>{{ __('app.client.settings.whatsapp.token_label') }}</label>
            <input type="password" name="whatsapp_access_token" value="{{ old('whatsapp_access_token', $business->whatsapp_access_token ?? '') }}">
            <p class="form-help">{{ __('app.client.settings.whatsapp.token_help') }}</p>
        </div>
        <div class="form-group">
            <label>{{ __('app.client.settings.whatsapp.account_id_label') }}</label>
            <input type="text" name="whatsapp_business_account_id" value="{{ old('whatsapp_business_account_id', $business->whatsapp_business_account_id ?? '') }}">
        </div>

        <hr style="margin:24px 0;border:none;border-top:1px solid var(--border);">

        <div class="form-group">
            <label>{{ __('app.client.settings.whatsapp.prompt_label') }}</label>
            <textarea name="gemini_system_prompt" id="gemini_prompt" rows="6" placeholder="{{ __('app.client.settings.whatsapp.prompt_placeholder') }}">{{ old('gemini_system_prompt', $business->gemini_system_prompt ?? '') }}</textarea>
            <p class="form-help">{{ __('app.client.settings.whatsapp.prompt_help') }}</p>
        </div>

        <div class="form-group">
            <label>{{ __('app.client.settings.whatsapp.documents_label') }}</label>
            <p class="form-help" style="margin-bottom:12px;">{{ __('app.client.settings.whatsapp.documents_help') }}</p>

            @if(!empty($business->ai_documents) && count($business->ai_documents) > 0)
                <div style="margin-bottom:12px;">
                    @foreach($business->ai_documents as $index => $doc)
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 12px;background:var(--light);border-radius:8px;margin-bottom:6px;">
                            <div style="display:flex;align-items:center;gap:8px;">
                                <span style="font-size:18px;">
                                    @if(str_contains($doc['type'] ?? '', 'pdf')) 📄
                                    @elseif(str_contains($doc['type'] ?? '', 'word') || str_contains($doc['type'] ?? '', 'document')) 📝
                                    @elseif(str_contains($doc['type'] ?? '', 'excel') || str_contains($doc['type'] ?? '', 'sheet')) 📊
                                    @elseif(str_contains($doc['type'] ?? '', 'powerpoint') || str_contains($doc['type'] ?? '', 'presentation')) 📑
                                    @else 📎
                                    @endif
                                </span>
                                <div>
                                    <div style="font-size:13px;font-weight:600;">{{ $doc['name'] }}</div>
                                    <div style="font-size:11px;color:var(--gray);">{{ round(($doc['size'] ?? 0) / 1024) }} KB</div>
                                </div>
                            </div>
                            <button type="submit" name="delete_document" value="{{ $index }}" class="btn btn-ghost btn-sm" style="color:var(--red);" onclick="return confirm('{{ __('app.client.settings.whatsapp.delete_doc_confirm') }}')">✕</button>
                        </div>
                    @endforeach
                </div>
            @endif

            <input type="file" name="ai_documents[]" multiple
                   accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.md"
                   style="display:block;width:100%;padding:12px;border:2px dashed var(--border);border-radius:8px;cursor:pointer;font-size:14px;"
                   onchange="this.style.borderColor='var(--sky)'">
            <p class="form-help" style="margin-top:6px;">{{ __('app.client.settings.whatsapp.accepted_formats') }}</p>
        </div>

        <div style="display:flex;gap:12px;margin-top:20px;">
            <button type="submit" class="btn btn-primary">{{ __('app.common.save') }}</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
@include('components.tinymce')
<script>initTinyMCE('#gemini_prompt', 300);</script>
@endsection
