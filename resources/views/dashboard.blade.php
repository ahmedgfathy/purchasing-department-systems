@extends('layouts.app')

@section('title', __('app.dashboard.title'))
@section('heading', __('app.dashboard.heading'))
@section('subheading', __('app.dashboard.subheading'))

@section('actions')
    @if (auth()->user()->hasPermission('users.create'))
        <a href="{{ route('users.create') }}" class="btn btn-primary">{{ __('app.users.new') }}</a>
    @endif
    @if (auth()->user()->hasPermission('audit.view'))
        <a href="{{ route('audit.index') }}" class="btn btn-secondary">{{ __('app.audit.title') }}</a>
    @endif
@endsection

@section('content')
    <div class="stat-grid">
        <div class="stat">
            <span>{{ __('app.dashboard.users') }}</span>
            <strong>{{ number_format($stats['users']) }}</strong>
        </div>
        <div class="stat">
            <span>{{ __('app.dashboard.roles') }}</span>
            <strong>{{ number_format($stats['roles']) }}</strong>
        </div>
        @if ($stats['audits_today'] !== null)
            <div class="stat">
                <span>{{ __('app.dashboard.audits_today') }}</span>
                <strong>{{ number_format($stats['audits_today']) }}</strong>
            </div>
            <div class="stat">
                <span>{{ __('app.dashboard.logins_today') }}</span>
                <strong>{{ number_format($stats['logins_today']) }}</strong>
            </div>
        @endif
    </div>

    @if (auth()->user()->hasPermission('audit.view'))
        <div class="card">
            <div class="card-header">
                <div>
                    <h2>{{ __('app.dashboard.recent') }}</h2>
                    <p>{{ __('app.dashboard.recent_sub') }}</p>
                </div>
                <a href="{{ route('audit.index') }}" class="btn btn-secondary btn-sm">{{ __('app.dashboard.view_all') }}</a>
            </div>

            @if ($recentAudits->isEmpty())
                <div class="empty">{{ __('app.audit.empty') }}</div>
            @else
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>{{ __('app.audit.col_when') }}</th>
                                <th>{{ __('app.audit.col_event') }}</th>
                                <th>{{ __('app.audit.col_who') }}</th>
                                <th>{{ __('app.audit.col_record') }}</th>
                                <th>{{ __('app.audit.col_where') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentAudits as $log)
                                <tr>
                                    <td>
                                        <div class="cell-title">{{ $log->created_at->translatedFormat(__('app.date.short')) }}</div>
                                        <div class="cell-sub">{{ $log->created_at->format('H:i:s') }}</div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $log->eventColor() }}">{{ $log->eventLabel() }}</span>
                                    </td>
                                    <td>
                                        <div class="cell-title">{{ $log->user_name ?? __('app.audit.guest_system') }}</div>
                                        <div class="cell-sub">
                                            @if ($log->user_employee_id)
                                                {{ __('app.users.employee_id_short', ['id' => $log->user_employee_id]) }}@if ($log->user_email) · {{ $log->user_email }}@endif
                                            @else
                                                {{ $log->user_email ?? '—' }}
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="cell-title">{{ $log->auditable_label ?? '—' }}</div>
                                        <div class="cell-sub">{{ $log->subjectType() }}{{ $log->auditable_id ? ' #'.$log->auditable_id : '' }}</div>
                                    </td>
                                    <td>
                                        <div class="cell-sub">{{ $log->ip_address ?? '—' }}</div>
                                        <a href="{{ route('audit.show', $log) }}" class="cell-sub" style="color:var(--text)">{{ __('app.audit.details') }}</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @else
        <div class="card">
            <div class="card-header">
                <div>
                    <h2>{{ __('app.dashboard.welcome', ['name' => auth()->user()->name]) }}</h2>
                    <p>{{ __('app.dashboard.welcome_sub') }}</p>
                </div>
            </div>
        </div>
    @endif
@endsection
