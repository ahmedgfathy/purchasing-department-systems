@extends('layouts.app')

@section('title', __('app.markets.create_title'))
@section('heading', __('app.markets.create_heading'))
@section('subheading', __('app.markets.create_subheading'))

@section('actions')
    <a href="{{ route('markets.index') }}" class="btn btn-ghost">{{ __('app.markets.back') }}</a>
@endsection

@section('content')
    <div class="card">
        @include('dropdowns.markets._form', [
            'action' => route('markets.store'),
            'method' => 'POST',
            'market' => null,
            'submitLabel' => __('app.markets.create_submit'),
        ])
    </div>
@endsection