@extends(backpack_view('blank'))

@php
    $defaultBreadcrumbs = [
        trans('backpack::crud.admin') => url(config('backpack.base.route_prefix'), 'dashboard'),
        $crud->entity_name_plural => url($crud->route),
        Str::of($crud->getCurrentOperation())->headline()->toString() => false,
    ];

    // if breadcrumbs aren't defined in the CrudController, use the default breadcrumbs
    $breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;
@endphp

@section('header')
<section class="header-operation container-fluid animated fadeIn d-flex mb-2 align-items-baseline d-print-none" bp-section="page-header">
    <h1 class="text-capitalize mb-0" bp-section="page-heading">{!! $crud->getHeading() ?? $crud->entity_name_plural !!}</h1>
    <p class="ms-2 ml-2 mb-0" bp-section="page-subheading">{!! $crud->getSubheading() ?? Str::of($operation)->headline() !!}.</p>
    @if ($crud->hasAccess('list'))
        <p class="mb-0 ms-2 ml-2" bp-section="page-subheading-back-button">
            <small><a href="{{ url($crud->route) }}" class="d-print-none font-sm"><i
                        class="la la-angle-double-{{ config('backpack.base.html_direction') == 'rtl' ? 'right' : 'left' }}"></i>
                    {{ trans('backpack::crud.back_to_all') }} <span>{{ $crud->entity_name_plural }}</span></a></small>
        </p>
    @endif
</section>
@endsection

@section('content')

<div class="row" bp-section="crud-operation-{{ Str::of($operation)->kebab() }}">
    <div class="{{ $crud->get($operation.'.contentClass') }}">
        {{-- Default box --}}

        @include('crud::inc.grouped_errors')

        <form
            method="{{ $formMethod ?? 'post' }}"
            action="{{ $formAction ?? url()->current() }}"
            @if ($crud->hasUploadFields())
            enctype="multipart/form-data"
            @endif
            >
            {!! csrf_field() !!}
            {!! method_field($formMethod ?? 'post') !!}

            {{-- load the view from the application if it exists, otherwise load the one in the package --}}
            @if(view()->exists('vendor.backpack.crud.form_content'))
                @include('vendor.backpack.crud.form_content', [ 'fields' => $crud->fields(), 'action' => $operation ])
            @else
                @include('crud::form_content', [ 'fields' => $crud->fields(), 'action' => $operation ])
            @endif
            {{-- This makes sure that all field assets are loaded. --}}
            <div class="d-none" id="parentLoadedAssets">{{ json_encode(Basset::loaded()) }}</div>
            @include('crud::inc.form_save_buttons')
        </form>
    </div>
</div>

@endsection
