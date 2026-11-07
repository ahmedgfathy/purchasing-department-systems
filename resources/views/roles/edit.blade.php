@extends('layouts.app')

@section('title', __('app.roles.edit_title'))
@section('heading', __('app.roles.edit_heading'))
@section('subheading', __('app.roles.edit_subheading'))

@section('actions')
    <a href="{{ route('roles.index') }}" class="btn btn-ghost">{{ __('app.roles.back') }}</a>
@endsection

@section('content')
    <div class="card">
        @include('roles._form', [
            'action' => route('roles.update', $role),
            'method' => 'PUT',
            'role' => $role,
            'permissions' => $permissions,
            'submitLabel' => __('app.roles.save'),
        ])
    </div>
@endsection
