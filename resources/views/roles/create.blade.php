@extends('layouts.app')

@section('title', __('app.roles.create_title'))
@section('heading', __('app.roles.create_heading'))
@section('subheading', __('app.roles.create_subheading'))

@section('actions')
    <a href="{{ route('roles.index') }}" class="btn btn-ghost">{{ __('app.roles.back') }}</a>
@endsection

@section('content')
    <div class="card">
        @include('roles._form', [
            'action' => route('roles.store'),
            'method' => 'POST',
            'role' => null,
            'permissions' => $permissions,
            'submitLabel' => __('app.roles.create_submit'),
        ])
    </div>
@endsection
