@extends(backpack_view('errors.layout'))

@php
  $error_number = 503;
  $shouldEscape = ! in_array('developer-error-exception', $exception->getHeaders());
@endphp

@section('title')
  {{ trans('backpack::base.error_page.503') }}
@endsection

@section('description')
  {!! $exception?->getMessage() && config('app.debug') ? ($shouldEscape ? e($exception->getMessage()) : $exception->getMessage()) : trans('backpack::base.error_page.message_503') !!}
@endsection
