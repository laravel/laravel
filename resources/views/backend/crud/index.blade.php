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
            {{ @$indexTitle ?: Str::plural($baseName) }} List
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        <x-nav-wire-link :href="route($modelCreateRoute)" :active="request()->routeIs($modelCreateRoute)" class="btn btn-primary btn-sm">
            Add {{ $baseName }}
        </x-nav-wire-link>

        <x-datatable-section>
            @includeFirst([sprintf("%s.datatable", $resourcePath), 'backend.crud.datatable'])
        </x-datatable-section>
    </div>

    @includeIf(sprintf("%s.index_scripts", $resourcePath))
</x-app-layout>
