@extends('layouts.app')

@section('title', __('app.roles.title'))
@section('heading', __('app.roles.heading'))
@section('subheading', __('app.roles.subheading'))

@section('actions')
    @if (auth()->user()->hasPermission('roles.create'))
        <a href="{{ route('roles.create') }}" class="btn btn-primary">{{ __('app.roles.new') }}</a>
    @endif
@endsection

@section('content')
    <div class="toolbar">
        <form method="GET" action="{{ route('roles.index') }}">
            <input type="search" name="q" value="{{ $search }}" placeholder="{{ __('app.roles.search_placeholder') }}" style="min-width:260px">
            <button type="submit" class="btn btn-secondary">{{ __('app.roles.search') }}</button>
            @if ($search !== '')
                <a href="{{ route('roles.index') }}" class="btn btn-ghost">{{ __('app.roles.clear') }}</a>
            @endif
        </form>
    </div>

    <div class="card" style="padding:0;overflow:hidden">
        @if ($roles->isEmpty())
            <div class="empty">{{ __('app.roles.empty') }}</div>
        @else
            <div class="table-wrap" style="border:0;border-radius:0">
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('app.roles.col_role') }}</th>
                            <th>{{ __('app.roles.col_users') }}</th>
                            <th>{{ __('app.roles.col_permissions') }}</th>
                            <th>{{ __('app.roles.col_type') }}</th>
                            <th style="width:1%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roles as $role)
                            <tr>
                                <td>
                                    <div class="cell-title">{{ $role->displayName() }}</div>
                                    <div class="cell-sub mono">{{ $role->slug }}</div>
                                    @if ($role->displayDescription())
                                        <div class="cell-sub">{{ $role->displayDescription() }}</div>
                                    @endif
                                </td>
                                <td>{{ $role->users_count }}</td>
                                <td>{{ $role->permissions_count }}</td>
                                <td>
                                    @if ($role->is_system)
                                        <span class="badge static">{{ __('app.roles.system') }}</span>
                                    @else
                                        <span class="badge slate">{{ __('app.roles.custom') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="actions">
                                        @if (auth()->user()->hasPermission('roles.update'))
                                            <a href="{{ route('roles.edit', $role) }}" class="btn btn-secondary btn-sm">{{ __('app.roles.edit') }}</a>
                                        @endif
                                        @if (auth()->user()->hasPermission('roles.delete') && ! $role->is_system)
                                            <form method="POST" action="{{ route('roles.destroy', $role) }}" class="inline-form" onsubmit="return confirm(@js(__('app.roles.delete_confirm', ['name' => $role->displayName()])))">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">{{ __('app.roles.delete') }}</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @include('partials.pagination', ['paginator' => $roles])
@endsection
