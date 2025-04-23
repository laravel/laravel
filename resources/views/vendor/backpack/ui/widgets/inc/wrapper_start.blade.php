@php
	$widget['wrapper']['element'] = $widget['wrapper']['element'] ?? 'div';
	$widget['wrapper']['class'] = $widget['wrapper']['class'] ?? "col-sm-6 col-md-4";

    // each wrapper attribute can be a callback or a string
    // for those that are callbacks, run the callbacks to get the final string to use
    foreach($widget['wrapper'] as $attribute => $value) {
        $widget['wrapper'][$attribute] = (!is_string($value) && is_callable($value) ? $value() : $value) ?? '';
    }
@endphp

<{{ $widget['wrapper']['element'] ?? 'div' }}
@foreach(Arr::where($widget['wrapper'],function($value, $key) { return $key != 'element'; }) as $element => $value)
    {{$element}}="{{$value}}"
@endforeach
>