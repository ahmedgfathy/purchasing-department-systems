@extends('layouts.app')

@section('title', __('app.markets.edit_title'))
@section('heading', __('app.markets.edit_heading'))
@section('subheading', __('app.markets.edit_subheading'))

@section('actions')
    <a href="{{ route('markets.index') }}" class="btn btn-ghost">{{ __('app.markets.back') }}</a>
@endsection

@section('content')
    <div class="card">
        @include('dropdowns.markets._form', [
            'action' => route('markets.update', $market),
            'method' => 'PUT',
            'market' => $market,
            'submitLabel' => __('app.markets.save'),
        ])
    </div>
@endsection