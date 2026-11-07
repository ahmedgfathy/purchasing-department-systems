@extends('layouts.app')

@section('title', __('app.rfq.edit_title'))
@section('heading', __('app.rfq.edit_heading'))
@section('subheading', __('app.rfq.edit_subheading', ['reference' => $rfq->reference]))

@section('actions')
    <a href="{{ route('rfqs.show', $rfq) }}" class="btn btn-ghost">{{ __('app.rfq.back_to_request') }}</a>
@endsection

@section('content')
    @include('rfqs._form', ['rfq' => $rfq])
@endsection
