@extends('layouts.app')

@section('content')
    <div class="grid grid-cols-2 gap-4">
        <livewire:projects.create-project />
        <livewire:projects.list-projects />
    </div>
@endsection

