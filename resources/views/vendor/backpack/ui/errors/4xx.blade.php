@extends(backpack_view('errors.layout'))

@php
  $error_number ??= 400;
  $shouldEscape = ! in_array('developer-error-exception', $exception->getHeaders());
@endphp

@section('title')
  {{ trans('backpack::base.error_page.'.$error_number) }}
@endsection

@section('description')
  {!! $exception?->getMessage() && config('app.debug') ? ($shouldEscape ? e($exception->getMessage()) : $exception->getMessage()) : trans('backpack::base.error_page.message_4xx', [
    'href_back' => 'href="javascript:history.back()"',
    'href_homepage' => 'href="'.url('').'"',
  ]) !!}
@endsection
