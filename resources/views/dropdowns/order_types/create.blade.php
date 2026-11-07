@extends('layouts.app')

@section('title', __('app.order_types.create_title'))
@section('heading', __('app.order_types.create_heading'))
@section('subheading', __('app.order_types.create_subheading'))

@section('actions')
    <a href="{{ route('order_types.index') }}" class="btn btn-ghost">{{ __('app.order_types.back') }}</a>
@endsection

@section('content')
    <div class="card">
        @include('dropdowns.order_types._form', [
            'action' => route('order_types.store'),
            'method' => 'POST',
            'orderType' => null,
            'submitLabel' => __('app.order_types.create_submit'),
        ])
    </div>
@endsection