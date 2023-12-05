@props(['inputName' => false, 'collectionName' => false])

@php
    $class = 'form-group';

    if ($inputName && isset($errors)) {
        if ($collectionName && $errors->$collectionName->get($inputName)) {
            $class .= ' error';
        }
        if ( $errors->get($inputName)) {
            $class .= ' error';
        }
    }
@endphp

<div {{ $attributes->merge(['class' => $class]) }}>
    {{ $slot }}
</div>
