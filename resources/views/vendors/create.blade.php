@extends('layouts.app')

@section('title', __('app.vendors.create_title'))
@section('heading', __('app.vendors.create_heading'))
@section('subheading', __('app.vendors.create_subheading'))

@section('actions')
    <a href="{{ route('vendors.index') }}" class="btn btn-ghost">{{ __('app.vendors.back') }}</a>
@endsection

@section('content')
    @include('vendors._form', [
        'action' => route('vendors.store'),
        'method' => 'POST',
        'vendor' => null,
        'submitLabel' => __('app.vendors.create_submit'),
    ])
@endsection
