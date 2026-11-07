@extends('layouts.app')

@section('title', __('app.proposed_methods.edit_title'))
@section('heading', __('app.proposed_methods.edit_heading'))
@section('subheading', __('app.proposed_methods.edit_subheading'))

@section('actions')
    <a href="{{ route('proposed_methods.index') }}" class="btn btn-ghost">{{ __('app.proposed_methods.back') }}</a>
@endsection

@section('content')
    <div class="card">
        @include('dropdowns.proposed_methods._form', [
            'action' => route('proposed_methods.update', $proposedMethod),
            'method' => 'PUT',
            'proposedMethod' => $proposedMethod,
            'submitLabel' => __('app.proposed_methods.save'),
        ])
    </div>
@endsection