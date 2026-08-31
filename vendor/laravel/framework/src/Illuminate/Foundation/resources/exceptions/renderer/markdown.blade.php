# {{ $exception->class() }} - {!! $exception->title() !!}

{!! $exception->message() !!}

PHP {{ PHP_VERSION }}
Laravel {{ app()->version() }}
{{ $exception->request()->httpHost() }}

## Stack Trace

@foreach($exception->frames() as $index => $frame)
{{ $index }} - {{ $frame->file() }}:{{ $frame->line() }}
@endforeach

## Request

{{ $exception->request()->method() }} {{ \Illuminate\Support\Str::start($exception->request()->path(), '/') }}

## Headers

@forelse ($exception->requestHeaders() as $key => $value)
* **{{ $key }}**: {!! $value !!}
@empty
No header data available.
@endforelse

## Route Context

@forelse($exception->applicationRouteContext() as $name => $value)
{{ $name }}: {!! $value !!}
@empty
No routing data available.
@endforelse

## Route Parameters

@if ($routeParametersContext = $exception->applicationRouteParametersContext())
{!! $routeParametersContext !!}
@else
No route parameter data available.
@endif

## Database Queries

@forelse ($exception->applicationQueries() as ['connectionName' => $connectionName, 'sql' => $sql, 'time' => $time])
* {{ $connectionName }} - {!! $sql !!} ({{ $time }} ms)
@empty
No database queries detected.
@endforelse
