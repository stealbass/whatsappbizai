<div style="max-height:400px;overflow-y:auto;display:flex;flex-direction:column;gap:8px;">
    @foreach($messages as $msg)
    <div style="background:#f8fafc;border-radius:8px;padding:10px 14px;border-left:3px solid {{ $msg->type === 'document' ? '#0ea5e9' : '#22c55e' }};">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
            <span style="font-size:12px;font-weight:600;color:#64748b;">
                {{ $msg->type === 'document' ? '📎' : '💬' }}
                {{ $msg->contact_name ?? $msg->to }}
                <span style="font-weight:400;">({{ $msg->to }})</span>
            </span>
            <div style="display:flex;align-items:center;gap:8px;">
                @if($msg->trigger)
                <span style="font-size:10px;background:#e2e8f0;padding:1px 7px;border-radius:10px;color:#64748b;">{{ $msg->trigger }}</span>
                @endif
                <span style="font-size:11px;color:#64748b;">{{ $msg->created_at->diffForHumans() }}</span>
            </div>
        </div>
        <div style="font-size:13px;color:#334155;white-space:pre-line;">{{ $msg->type === 'document' ? '📄 '.$msg->content : \Illuminate\Support\Str::limit($msg->content, 300) }}</div>
    </div>
    @endforeach
</div>
