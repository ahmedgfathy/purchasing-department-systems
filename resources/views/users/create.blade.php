@extends('layouts.app')

@section('title', __('app.users.create_title'))
@section('heading', __('app.users.create_heading'))
@section('subheading', __('app.users.create_subheading'))

@section('actions')
    <a href="{{ route('users.index') }}" class="btn btn-ghost">{{ __('app.users.back') }}</a>
@endsection

@section('content')
    <div class="card">
        @include('users._form', [
            'action' => route('users.store'),
            'method' => 'POST',
            'user' => null,
            'roles' => $roles,
            'submitLabel' => __('app.users.create_submit'),
        ])
    </div>
@endsection
