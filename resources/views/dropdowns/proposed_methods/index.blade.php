@extends('layouts.app')

@section('title', __('app.proposed_methods.title'))
@section('heading', __('app.proposed_methods.heading'))
@section('subheading', __('app.proposed_methods.subheading'))

@section('actions')
    @if (auth()->user()->hasPermission('proposed_methods.manage'))
        <a href="{{ route('proposed_methods.create') }}" class="btn btn-primary">{{ __('app.proposed_methods.new') }}</a>
    @endif
@endsection

@section('content')
    <div class="toolbar">
        <form method="GET" action="{{ route('proposed_methods.index') }}">
            <input type="search" name="q" value="{{ $search }}" placeholder="{{ __('app.proposed_methods.search_placeholder') }}" style="min-width:260px">
            <button type="submit" class="btn btn-secondary">{{ __('app.proposed_methods.search') }}</button>
            @if ($search !== '')
                <a href="{{ route('proposed_methods.index') }}" class="btn btn-ghost">{{ __('app.proposed_methods.clear') }}</a>
            @endif
        </form>
    </div>

    @if ($proposedMethods->isEmpty())
        <div class="empty">{{ __('app.proposed_methods.empty') }}</div>
    @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>{{ __('app.proposed_methods.code') }}</th>
                        <th>{{ __('app.proposed_methods.name') }}</th>
                        <th>{{ __('app.proposed_methods.status') }}</th>
                        <th>{{ __('app.proposed_methods.created_by') }}</th>
                        @if (auth()->user()->hasPermission('proposed_methods.manage'))
                            <th style="width:1%"></th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($proposedMethods as $proposedMethod)
                        <tr>
                            <td>
                                <div class="cell-title">{{ $proposedMethod->code }}</div>
                            </td>
                            <td>
                                <div class="cell-title">{{ $proposedMethod->name }}</div>
                            </td>
                            <td><span class="badge {{ $proposedMethod->status === \App\Models\ProposedMethod::STATUS_ACTIVE ? 'green' : 'slate' }}">{{ $proposedMethod->statusLabel() }}</span></td>
                            <td class="cell-sub">{{ $proposedMethod->creator?->name ?? '—' }}</td>
                            @if (auth()->user()->hasPermission('proposed_methods.manage'))
                                <td>
                                    <div style="display:flex;gap:6px;align-items:center">
                                        <a href="{{ route('proposed_methods.edit', $proposedMethod) }}" class="btn btn-secondary btn-sm">{{ __('app.proposed_methods.edit') }}</a>
                                        <form method="POST" action="{{ route('proposed_methods.destroy', $proposedMethod) }}" onsubmit="return confirm('{{ __('app.proposed_methods.delete_confirm') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">{{ __('app.proposed_methods.delete') }}</button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @include('partials.pagination', ['paginator' => $proposedMethods])
@endsection