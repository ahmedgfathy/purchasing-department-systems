@extends('layouts.app')

@section('title', __('app.audit.title'))
@section('heading', __('app.audit.heading'))
@section('subheading', __('app.audit.subheading'))

@section('content')
    <div class="card">
        <form method="GET" action="{{ route('audit.index') }}">
            <div class="form-grid" style="grid-template-columns:repeat(3,minmax(0,1fr))">
                <div class="field">
                    <label for="q">{{ __('app.audit.search') }}</label>
                    <input id="q" type="search" name="q" value="{{ $filters['q'] }}" placeholder="{{ __('app.audit.search_placeholder') }}">
                </div>

                <div class="field">
                    <label for="event">{{ __('app.audit.event') }}</label>
                    <select id="event" name="event">
                        <option value="">{{ __('app.audit.all_events') }}</option>
                        @foreach ($events as $event)
                            <option value="{{ $event }}" @selected($filters['event'] === $event)>{{ __('app.audit_events.'.$event) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="type">{{ __('app.audit.type') }}</label>
                    <select id="type" name="type">
                        <option value="">{{ __('app.audit.all_types') }}</option>
                        @foreach ($types as $type)
                            <option value="{{ $type }}" @selected($filters['type'] === $type)>{{ __('app.models.'.class_basename($type)) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="from">{{ __('app.audit.from') }}</label>
                    <input id="from" type="date" name="from" value="{{ $filters['from'] }}">
                </div>

                <div class="field">
                    <label for="to">{{ __('app.audit.to') }}</label>
                    <input id="to" type="date" name="to" value="{{ $filters['to'] }}">
                </div>

                <div class="field" style="display:flex;align-items:flex-end;gap:8px">
                    <button type="submit" class="btn btn-primary">{{ __('app.audit.filter') }}</button>
                    <a href="{{ route('audit.index') }}" class="btn btn-ghost">{{ __('app.audit.reset') }}</a>
                </div>
            </div>
        </form>
    </div>

    <div class="card" style="padding:0;overflow:hidden;margin-top:20px">
        @if ($logs->isEmpty())
            <div class="empty">{{ __('app.audit.empty') }}</div>
        @else
            <div class="table-wrap" style="border:0;border-radius:0">
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('app.audit.col_when') }}</th>
                            <th>{{ __('app.audit.col_event') }}</th>
                            <th>{{ __('app.audit.col_who') }}</th>
                            <th>{{ __('app.audit.col_record') }}</th>
                            <th>{{ __('app.audit.col_where') }}</th>
                            <th style="width:1%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logs as $log)
                            <tr>
                                <td>
                                    <div class="cell-title">{{ $log->created_at->translatedFormat(__('app.date.short')) }}</div>
                                    <div class="cell-sub">{{ $log->created_at->format('H:i:s') }}</div>
                                </td>
                                <td><span class="badge {{ $log->eventColor() }}">{{ $log->eventLabel() }}</span></td>
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
                                    <div class="cell-sub">{{ $log->http_method }} {{ \Illuminate\Support\Str::limit($log->url, 42) }}</div>
                                </td>
                                <td>
                                    <a href="{{ route('audit.show', $log) }}" class="btn btn-secondary btn-sm">{{ __('app.audit.details') }}</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @include('partials.pagination', ['paginator' => $logs])
@endsection
