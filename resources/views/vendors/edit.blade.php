@extends('layouts.app')

@section('title', __('app.vendors.edit_title'))
@section('heading', __('app.vendors.edit_heading'))
@section('subheading', __('app.vendors.edit_subheading'))

@section('actions')
    <a href="{{ route('vendors.index') }}" class="btn btn-ghost">{{ __('app.vendors.back') }}</a>
@endsection

@section('content')
    <div class="card">
        @include('vendors._form', [
            'action' => route('vendors.update', $vendor),
            'method' => 'PUT',
            'vendor' => $vendor,
            'submitLabel' => __('app.vendors.save'),
        ])
    </div>
@endsection
