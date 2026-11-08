@extends('layouts.app')

@section('title', __('app.administration.title'))
@section('heading', __('app.administration.heading'))
@section('subheading', __('app.administration.subheading'))

@section('content')
    <div class="card">
        <div class="card-header"><div><h2>{{ __('app.administration.dropdown_title') }}</h2><p>{{ __('app.administration.dropdown_subheading') }}</p></div></div>
        <div class="stat-grid">
            @foreach ($lookups as $type => $lookup)
                <a class="stat" href="{{ route('administration.lookup.index', $type) }}"><span>{{ $lookup['title'] }}</span><strong>→</strong></a>
            @endforeach
            @if (auth()->user()->hasPermission('markets.view'))<a class="stat" href="{{ route('markets.index') }}"><span>{{ __('app.administration.markets') }}</span><strong>→</strong></a>@endif
            @if (auth()->user()->hasPermission('order_types.view'))<a class="stat" href="{{ route('order_types.index') }}"><span>{{ __('app.administration.order_types') }}</span><strong>→</strong></a>@endif
            @if (auth()->user()->hasPermission('proposed_methods.view'))<a class="stat" href="{{ route('proposed_methods.index') }}"><span>{{ __('app.administration.proposed_methods') }}</span><strong>→</strong></a>@endif
        </div>
    </div>
@endsection
