@extends('client.layout')
@section('title', __('app.client.dashboard.title'))

@section('content')
<div class="welcome" style="margin-bottom:24px;">
    <h1 style="font-size:24px;font-weight:800;">{{ __('app.client.dashboard.welcome') }}, {{ explode(' ', $user->name)[0] }} 👋</h1>
    <p style="color:var(--gray);font-size:14px;">{{ __('app.client.dashboard.summary') }}</p>
</div>

{{-- ── WhatsApp status banner ───────────────────────────────── --}}
@if(!$business)
<div class="alert alert-warning" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:24px;">
    <div>
        <strong>📱 {{ __('app.client.dashboard.setup_title') }}</strong><br>
        <span style="font-size:13px;">{{ __('app.client.dashboard.setup_desc') }}</span>
    </div>
    <a href="{{ url('client/settings/whatsapp') }}" class="btn btn-primary btn-sm">{{ __('app.client.dashboard.setup_btn') }}</a>
</div>
@elseif($business->sandbox_mode)
<div class="alert" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:24px;background:#fffbeb;border:1px solid #fde68a;color:#92400e;">
    <div style="display:flex;align-items:center;gap:10px;">
        <span style="font-size:22px;">🧪</span>
        <div>
            <strong>{{ __('app.client.dashboard.sandbox_title') }}</strong><br>
            <span style="font-size:13px;">{{ __('app.client.dashboard.sandbox_desc') }}</span>
        </div>
    </div>
    <a href="{{ url('client/settings/whatsapp') }}" class="btn btn-sm" style="background:#f59e0b;color:#fff;border:none;flex-shrink:0;">
        {{ __('app.client.dashboard.sandbox_btn') }}
    </a>
</div>
@elseif(!$business->whatsapp_phone_number_id)
<div class="alert alert-warning" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:24px;">
    <div>
        <strong>📱 {{ __('app.client.dashboard.setup_title') }}</strong><br>
        <span style="font-size:13px;">{{ __('app.client.dashboard.setup_desc') }}</span>
    </div>
    <a href="{{ url('client/settings/whatsapp') }}" class="btn btn-primary btn-sm">{{ __('app.client.dashboard.setup_btn') }}</a>
</div>
@endif

{{-- ── Onboarding checklist ────────────────────────────────────
     Visible tant que $showOnboarding est vrai
     Disparaît automatiquement une fois les 5 étapes complètes
──────────────────────────────────────────────────────────────── --}}
@if($showOnboarding)
<div id="onboarding-card" style="background:#fff;border:1px solid var(--border);border-radius:16px;margin-bottom:32px;overflow:hidden;">

    {{-- Header --}}
    <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;gap:12px;">
        <div>
            <h2 style="font-size:17px;font-weight:800;margin-bottom:2px;">
                🚀 {{ __('app.client.dashboard.onboarding_title') }}
            </h2>
            <p style="font-size:13px;color:var(--gray);margin:0;">{{ __('app.client.dashboard.onboarding_desc') }}</p>
        </div>
        <div style="display:flex;align-items:center;gap:16px;flex-shrink:0;">
            {{-- Progress counter --}}
            <div style="text-align:center;">
                <div style="font-size:22px;font-weight:800;color:var(--sky);">{{ $onboardingDone }}/{{ $onboardingTotal }}</div>
                <div style="font-size:11px;color:var(--gray);white-space:nowrap;">{{ __('app.client.dashboard.onboarding_steps') }}</div>
            </div>
            {{-- Dismiss --}}
            <a href="{{ url('client/dashboard/dismiss-onboarding') }}"
               style="font-size:12px;color:var(--gray);text-decoration:none;white-space:nowrap;padding:6px 10px;border:1px solid var(--border);border-radius:8px;">
                {{ __('app.client.dashboard.onboarding_dismiss') }}
            </a>
        </div>
    </div>

    {{-- Progress bar --}}
    <div style="height:4px;background:#f1f5f9;">
        <div style="height:100%;background:var(--sky);width:{{ ($onboardingDone/$onboardingTotal)*100 }}%;transition:width .4s;"></div>
    </div>

    {{-- Steps --}}
    @php
    $steps = [
        [
            'key'   => 'profile',
            'icon'  => '🏢',
            'title' => __('app.client.dashboard.step_profile_title'),
            'desc'  => __('app.client.dashboard.step_profile_desc'),
            'cta'   => __('app.client.dashboard.step_profile_cta'),
            'url'   => url('client/settings/business'),
        ],
        [
            'key'   => 'contact',
            'icon'  => '👥',
            'title' => __('app.client.dashboard.step_contact_title'),
            'desc'  => __('app.client.dashboard.step_contact_desc'),
            'cta'   => __('app.client.dashboard.step_contact_cta'),
            'url'   => url('client/contacts/create'),
        ],
        [
            'key'   => 'document',
            'icon'  => '🧾',
            'title' => __('app.client.dashboard.step_document_title'),
            'desc'  => __('app.client.dashboard.step_document_desc'),
            'cta'   => __('app.client.dashboard.step_document_cta'),
            'url'   => url('client/invoices/create'),
        ],
        [
            'key'   => 'sent',
            'icon'  => '📤',
            'title' => __('app.client.dashboard.step_sent_title'),
            'desc'  => __('app.client.dashboard.step_sent_desc'),
            'cta'   => __('app.client.dashboard.step_sent_cta'),
            'url'   => url('client/invoices'),
        ],
        [
            'key'   => 'whatsapp',
            'icon'  => '📲',
            'title' => __('app.client.dashboard.step_whatsapp_title'),
            'desc'  => __('app.client.dashboard.step_whatsapp_desc'),
            'cta'   => __('app.client.dashboard.step_whatsapp_cta'),
            'url'   => url('client/settings/whatsapp'),
        ],
    ];
    // Find the first incomplete step index for "current" highlight
    $currentIdx = null;
    foreach ($steps as $i => $s) {
        if (!$onboarding[$s['key']]) { $currentIdx = $i; break; }
    }
    @endphp

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));">
        @foreach($steps as $i => $step)
        @php
            $done    = $onboarding[$step['key']];
            $current = $currentIdx === $i;
        @endphp
        <div style="padding:20px;border-right:1px solid var(--border);@if($i === count($steps)-1) border-right:none; @endif position:relative;
                    @if($current) background:#f0f9ff; @endif">

            {{-- Step number / checkmark --}}
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
                @if($done)
                    <div style="width:28px;height:28px;border-radius:50%;background:#22c55e;color:#fff;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;flex-shrink:0;">✓</div>
                @elseif($current)
                    <div style="width:28px;height:28px;border-radius:50%;background:var(--sky);color:#fff;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800;flex-shrink:0;">{{ $i+1 }}</div>
                @else
                    <div style="width:28px;height:28px;border-radius:50%;border:2px solid var(--border);color:var(--gray);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:600;flex-shrink:0;">{{ $i+1 }}</div>
                @endif
                <span style="font-size:20px;">{{ $step['icon'] }}</span>
            </div>

            {{-- Title --}}
            <div style="font-size:13px;font-weight:700;margin-bottom:4px;
                        @if($done) color:var(--gray);text-decoration:line-through; @elseif($current) color:var(--dark); @else color:var(--gray); @endif">
                {{ $step['title'] }}
            </div>

            {{-- Description --}}
            @if(!$done)
            <p style="font-size:12px;color:var(--gray);margin-bottom:12px;line-height:1.5;">{{ $step['desc'] }}</p>
            @endif

            {{-- CTA --}}
            @if(!$done)
            <a href="{{ $step['url'] }}"
               style="display:inline-block;font-size:12px;font-weight:700;padding:6px 12px;border-radius:8px;text-decoration:none;
                      @if($current) background:var(--sky);color:#fff; @else background:#f1f5f9;color:var(--gray); @endif">
                {{ $step['cta'] }} →
            </a>
            @else
            <span style="font-size:12px;color:#22c55e;font-weight:600;">✓ {{ __('app.client.dashboard.onboarding_step_done') }}</span>
            @endif
        </div>
        @endforeach
    </div>
</div>
@elseif($business && !session('onboarding_dismissed', false))
{{-- All steps done — celebrate briefly --}}
<div id="onboarding-complete" style="background:#f0fdf4;border:1px solid #86efac;border-radius:12px;padding:14px 20px;margin-bottom:24px;display:flex;align-items:center;gap:12px;">
    <span style="font-size:24px;">🎉</span>
    <div style="font-size:14px;font-weight:600;color:#15803d;">{{ __('app.client.dashboard.onboarding_done') }}</div>
    <a href="{{ url('client/dashboard/dismiss-onboarding') }}"
       style="margin-left:auto;font-size:12px;color:#15803d;text-decoration:none;padding:4px 10px;border:1px solid #86efac;border-radius:6px;">
        ✕
    </a>
</div>
@endif

{{-- ── Stats grid ──────────────────────────────────────────────── --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">👥</div>
        <div class="stat-value">{{ number_format($stats['contacts']) }}</div>
        <div class="stat-label">{{ __('app.client.dashboard.contacts') }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🧾</div>
        <div class="stat-value">{{ number_format($stats['invoices']) }}</div>
        <div class="stat-label">{{ __('app.client.dashboard.invoices') }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">📄</div>
        <div class="stat-value">{{ number_format($stats['quotes']) }}</div>
        <div class="stat-label">{{ __('app.client.dashboard.quotes') }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">💬</div>
        <div class="stat-value">{{ number_format($stats['conversations']) }}</div>
        <div class="stat-label">{{ __('app.client.dashboard.conversations') }}</div>
    </div>
</div>

{{-- ── Recent invoices + quotes ─────────────────────────────────── --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">
    <div class="card">
        <div class="card-header">
            <h2>🧾 {{ __('app.client.dashboard.recent_invoices') }}</h2>
            <a href="{{ url('client/invoices') }}" class="btn btn-ghost btn-sm">{{ __('app.client.dashboard.view_all') }}</a>
        </div>
        @if($recentInvoices->isEmpty())
            <div class="empty">
                <div class="empty-icon">🧾</div>
                <p>{{ __('app.client.dashboard.no_invoices') }}</p>
                <a href="{{ url('client/invoices/create') }}" class="btn btn-primary btn-sm">{{ __('app.client.dashboard.create_invoice') }}</a>
            </div>
        @else
            <table>
                <thead><tr>
                    <th>{{ __('app.client.invoices.number') }}</th>
                    <th>{{ __('app.client.invoices.client') }}</th>
                    <th>{{ __('app.client.invoices.amount') }}</th>
                    <th>{{ __('app.client.invoices.status') }}</th>
                </tr></thead>
                <tbody>
                    @foreach($recentInvoices as $inv)
                    <tr>
                        <td><a href="{{ url('client/invoices/' . $inv->id) }}" style="color:var(--sky);text-decoration:none;font-weight:600;">{{ $inv->number }}</a></td>
                        <td>{{ $inv->contact->name ?? '-' }}</td>
                        <td>{{ number_format($inv->total, 0, ',', ' ') }} {{ $business->currency ?? 'XAF' }}</td>
                        <td><span class="status {{ $inv->status }}">{{ $inv->status }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="card">
        <div class="card-header">
            <h2>📄 {{ __('app.client.dashboard.recent_quotes') }}</h2>
            <a href="{{ url('client/quotes') }}" class="btn btn-ghost btn-sm">{{ __('app.client.dashboard.view_all') }}</a>
        </div>
        @if($recentQuotes->isEmpty())
            <div class="empty">
                <div class="empty-icon">📄</div>
                <p>{{ __('app.client.dashboard.no_quotes') }}</p>
                <a href="{{ url('client/quotes/create') }}" class="btn btn-primary btn-sm">{{ __('app.client.dashboard.create_quote') }}</a>
            </div>
        @else
            <table>
                <thead><tr>
                    <th>{{ __('app.client.quotes.number') }}</th>
                    <th>{{ __('app.client.quotes.client') }}</th>
                    <th>{{ __('app.client.quotes.amount') }}</th>
                    <th>{{ __('app.client.quotes.status') }}</th>
                </tr></thead>
                <tbody>
                    @foreach($recentQuotes as $q)
                    <tr>
                        <td><a href="{{ url('client/quotes/' . $q->id) }}" style="color:var(--sky);text-decoration:none;font-weight:600;">{{ $q->number }}</a></td>
                        <td>{{ $q->contact->name ?? '-' }}</td>
                        <td>{{ number_format($q->total, 0, ',', ' ') }} {{ $business->currency ?? 'XAF' }}</td>
                        <td><span class="status {{ $q->status }}">{{ $q->status }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

@endsection
