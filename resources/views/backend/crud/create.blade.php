{{-- !!! BE ALERT !!! --}}
{{-- !!!!!! BEWARE THIS IS A COMMON FOR ALL PAGES !!!!!--}}
{{--BEFORE YOU WRITE ANY CODE HERE - DO VERY CAREFULLY--}}
{{--OTHERWISE IT WILL AFFECT ALL PAGE--}}
{{--HOPE EVERY ONE READ THIS COMMENT :)--}}
{{--CONFIRM WITH PRAKASH BEFORE YOU CHANGE SOMETHING HERE--}}
@php
    $baseName = class_basename($modelClass);
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ sprintf("Add %s", $baseName) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                @includeFirst([sprintf("%s.form", $resourcePath), 'backend.crud.form'])
            </div>
        </div>
    </div>
</x-app-layout>
