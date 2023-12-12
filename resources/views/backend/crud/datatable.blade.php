{{-- !!! BE ALERT !!! --}}
{{-- !!!!!! BEWARE THIS IS A COMMON FOR ALL PAGES !!!!!--}}
{{--BEFORE YOU WRITE ANY CODE HERE - DO VERY CAREFULLY--}}
{{--OTHERWISE IT WILL AFFECT ALL PAGE--}}
{{--HOPE EVERY ONE READ THIS COMMENT :)--}}
{{--CONFIRM WITH PRAKASH BEFORE YOU CHANGE SOMETHING HERE--}}

<div class="container">
    <div class="card">
        <div class="card-header">
            {{ sprintf("Manage %s", Str::plural(class_basename($modelClass))) }}
            <x-nav-wire-link :href="route($modelCreateRoute)" :active="request()->routeIs($modelCreateRoute)" class="btn btn-primary btn-sm">
                Add {{ $baseName }}
            </x-nav-wire-link>
        </div>
        <div class="card-body">
            {{ $dataTable->table(['id' => 'datatable-buttons']) }}
        </div>
    </div>
</div>

@push('scripts')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}

    <script type="module">
        Livewire.on('refresh-datatable', () => {
            $('#datatable-buttons').DataTable().draw(false);
        });
    </script>
@endpush
