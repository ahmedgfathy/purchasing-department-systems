@extends('layouts.app')

@section('title', __('app.proposed_methods.create_title'))
@section('heading', __('app.proposed_methods.create_heading'))
@section('subheading', __('app.proposed_methods.create_subheading'))

@section('actions')
    <a href="{{ route('proposed_methods.index') }}" class="btn btn-ghost">{{ __('app.proposed_methods.back') }}</a>
@endsection

@section('content')
    <div class="card">
        @include('dropdowns.proposed_methods._form', [
            'action' => route('proposed_methods.store'),
            'method' => 'POST',
            'proposedMethod' => null,
            'submitLabel' => __('app.proposed_methods.create_submit'),
        ])
    </div>
@endsection