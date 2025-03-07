@extends('layouts.app')

@section('content')
    <div class="grid grid-cols-2 gap-4">
        <livewire:customers.create-customer />
        <livewire:customers.list-customers />
    </div>
@endsection

