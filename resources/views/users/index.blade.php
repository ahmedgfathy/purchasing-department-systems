@extends('layouts.app')

@section('title', __('app.users.title'))
@section('heading', __('app.users.heading'))
@section('subheading', __('app.users.subheading'))

@section('actions')
    @if (auth()->user()->hasPermission('users.create'))
        <a href="{{ route('users.create') }}" class="btn btn-primary">{{ __('app.users.new') }}</a>
    @endif
@endsection

@section('content')
    <div class="toolbar">
        <form method="GET" action="{{ route('users.index') }}">
            <input type="search" name="q" value="{{ $search }}" placeholder="{{ __('app.users.search_placeholder') }}" style="min-width:260px">
            <button type="submit" class="btn btn-secondary">{{ __('app.users.search') }}</button>
            @if ($search !== '')
                <a href="{{ route('users.index') }}" class="btn btn-ghost">{{ __('app.users.clear') }}</a>
            @endif
        </form>
    </div>

    <div class="card" style="padding:0;overflow:hidden">
        @if ($users->isEmpty())
            <div class="empty">{{ __('app.users.empty') }}</div>
        @else
            <div class="table-wrap" style="border:0;border-radius:0">
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('app.users.col_user') }}</th>
                            <th>{{ __('app.users.col_roles') }}</th>
                            <th>{{ __('app.users.col_last_login') }}</th>
                            <th>{{ __('app.users.col_created') }}</th>
                            <th style="width:1%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>
                                    <div class="cell-title">{{ $user->name }}</div>
                                    <div class="cell-sub">
                                        {{ __('app.users.employee_id_short', ['id' => $user->employee_id]) }}@if ($user->email) · {{ $user->email }}@endif
                                    </div>
                                </td>
                                <td>
                                    <div class="role-tags">
                                        @forelse ($user->roles as $role)
                                            <span class="badge static">{{ $role->displayName() }}</span>
                                        @empty
                                            <span class="cell-sub">{{ __('app.users.no_role') }}</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td>
                                    @if ($user->last_login_at)
                                        <div class="cell-title">{{ $user->last_login_at->translatedFormat(__('app.date.short')) }} {{ $user->last_login_at->format('H:i') }}</div>
                                        <div class="cell-sub">{{ $user->last_login_ip }}</div>
                                    @else
                                        <span class="cell-sub">{{ __('app.users.never') }}</span>
                                    @endif
                                </td>
                                <td class="cell-sub">{{ $user->created_at->translatedFormat(__('app.date.short')) }}</td>
                                <td>
                                    <div class="actions">
                                        @if (auth()->user()->hasPermission('users.update'))
                                            <a href="{{ route('users.edit', $user) }}" class="btn btn-secondary btn-sm">{{ __('app.users.edit') }}</a>
                                        @endif
                                        @if (auth()->user()->hasPermission('users.delete') && ! auth()->user()->is($user))
                                            <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline-form" onsubmit="return confirm(@js(__('app.users.delete_confirm', ['name' => $user->name])))">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">{{ __('app.users.delete') }}</button>
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

    @include('partials.pagination', ['paginator' => $users])
@endsection
