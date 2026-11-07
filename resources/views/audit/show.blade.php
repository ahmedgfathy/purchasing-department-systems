@extends('layouts.app')

@section('title', __('app.audit.entry_title', ['id' => $auditLog->id]))
@section('heading', __('app.audit.entry_title', ['id' => $auditLog->id]))
@section('subheading', __('app.audit.entry_subheading'))

@section('actions')
    <a href="{{ route('audit.index') }}" class="btn btn-ghost">{{ __('app.audit.back') }}</a>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div>
                <h2>{{ $auditLog->eventLabel() }}</h2>
                <p>{{ $auditLog->created_at->translatedFormat(__('app.date.long')) }} ({{ $auditLog->created_at->diffForHumans() }})</p>
            </div>
            <span class="badge {{ $auditLog->eventColor() }}">{{ $auditLog->eventLabel() }}</span>
        </div>

        <div class="detail-grid">
            <div class="detail">
                <dt>{{ __('app.audit.who') }}</dt>
                <dd>{{ $auditLog->user_name ?? __('app.audit.guest_system') }}</dd>
            </div>
            <div class="detail">
                <dt>{{ __('app.audit.email') }}</dt>
                <dd>{{ $auditLog->user_email ?? '—' }}</dd>
            </div>
            <div class="detail">
                <dt>{{ __('app.audit.employee_id') }}</dt>
                <dd class="mono">{{ $auditLog->user_employee_id ?? '—' }}</dd>
            </div>
            <div class="detail">
                <dt>{{ __('app.audit.record') }}</dt>
                <dd>{{ $auditLog->auditable_label ?? '—' }}</dd>
            </div>
            <div class="detail">
                <dt>{{ __('app.audit.record_type') }}</dt>
                <dd class="mono">{{ $auditLog->subjectType() }}{{ $auditLog->auditable_id ? ' #'.$auditLog->auditable_id : '' }}</dd>
            </div>
            <div class="detail">
                <dt>{{ __('app.audit.ip') }}</dt>
                <dd class="mono">{{ $auditLog->ip_address ?? '—' }}</dd>
            </div>
            <div class="detail">
                <dt>{{ __('app.audit.when') }}</dt>
                <dd class="mono">{{ $auditLog->created_at->toIso8601String() }}</dd>
            </div>
            <div class="detail">
                <dt>{{ __('app.audit.request') }}</dt>
                <dd class="mono">{{ $auditLog->http_method ?? '—' }}</dd>
            </div>
            <div class="detail">
                <dt>{{ __('app.audit.url') }}</dt>
                <dd class="mono" style="word-break:break-all">{{ $auditLog->url ?? '—' }}</dd>
            </div>
            <div class="detail" style="grid-column:1/-1">
                <dt>{{ __('app.audit.user_agent') }}</dt>
                <dd style="word-break:break-all">{{ $auditLog->user_agent ?? '—' }}</dd>
            </div>
        </div>

        <div class="two-col">
            <div>
                <div class="card-header"><h2>{{ __('app.audit.old_values') }}</h2></div>
                @if ($auditLog->old_values)
                    <pre class="code">{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
                @else
                    <div class="empty" style="border:1px solid var(--border);border-radius:10px">{{ __('app.audit.no_old') }}</div>
                @endif
            </div>

            <div>
                <div class="card-header"><h2>{{ __('app.audit.new_values') }}</h2></div>
                @if ($auditLog->new_values)
                    <pre class="code">{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
                @else
                    <div class="empty" style="border:1px solid var(--border);border-radius:10px">{{ __('app.audit.no_new') }}</div>
                @endif
            </div>
        </div>
    </div>
@endsection
