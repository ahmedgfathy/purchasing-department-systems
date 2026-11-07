@extends('layouts.app')

@section('title', __('app.rfq.create_title'))
@section('heading', __('app.rfq.create_heading'))
@section('subheading', __('app.rfq.create_subheading'))

@section('actions')
    <a href="{{ route('rfqs.index') }}" class="btn btn-ghost">{{ __('app.rfq.back') }}</a>
@endsection

@section('content')
    @include('rfqs._form', ['rfq' => null])
@endsection
