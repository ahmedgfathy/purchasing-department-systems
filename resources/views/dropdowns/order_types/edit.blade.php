@extends('layouts.app')

@section('title', __('app.order_types.edit_title'))
@section('heading', __('app.order_types.edit_heading'))
@section('subheading', __('app.order_types.edit_subheading'))

@section('actions')
    <a href="{{ route('order_types.index') }}" class="btn btn-ghost">{{ __('app.order_types.back') }}</a>
@endsection

@section('content')
    <div class="card">
        @include('dropdowns.order_types._form', [
            'action' => route('order_types.update', $orderType),
            'method' => 'PUT',
            'orderType' => $orderType,
            'submitLabel' => __('app.order_types.save'),
        ])
    </div>
@endsection