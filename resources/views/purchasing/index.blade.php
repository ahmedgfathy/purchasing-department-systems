@extends('layouts.app')

@php
    $meta = __('app.purchasing.'.$section);
@endphp

@section('title', $meta['title'])
@section('heading', $meta['heading'])
@section('subheading', $meta['subheading'])

@section('content')
    <div class="card">
        <div class="card-header">
            <div>
                <h2>{{ $meta['title'] }}</h2>
                <p>{{ $meta['description'] }}</p>
            </div>
            <span class="badge static">{{ __('app.purchasing.coming_soon') }}</span>
        </div>

        <div class="empty" style="border:1px dashed var(--border);border-radius:12px;padding:52px 24px">
            <div style="font-size:38px;line-height:1;margin-bottom:14px">{{ $meta['icon'] }}</div>
            <strong style="display:block;font-size:17px;margin-bottom:6px">{{ __('app.purchasing.placeholder_title') }}</strong>
            <span>{{ __('app.purchasing.placeholder_text') }}</span>
        </div>

        <div class="form-grid" style="margin-top:20px">
            <div class="detail">
                <dt>{{ __('app.purchasing.next_title') }}</dt>
                <dd>{{ $meta['next'] }}</dd>
            </div>
            <div class="detail">
                <dt>{{ __('app.purchasing.flow_title') }}</dt>
                <dd>{{ __('app.purchasing.flow_text') }}</dd>
            </div>
        </div>
    </div>
@endsection
