@extends('layouts.app')

@section('title', __('app.users.edit_title'))
@section('heading', __('app.users.edit_heading'))
@section('subheading', __('app.users.edit_subheading'))

@section('actions')
    <a href="{{ route('users.index') }}" class="btn btn-ghost">{{ __('app.users.back') }}</a>
@endsection

@section('content')
    <div class="card">
        @include('users._form', [
            'action' => route('users.update', $user),
            'method' => 'PUT',
            'user' => $user,
            'roles' => $roles,
            'submitLabel' => __('app.users.save'),
        ])
    </div>
@endsection
