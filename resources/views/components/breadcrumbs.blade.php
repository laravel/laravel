@props(['breadcrumbRoute'])

@if ($breadcrumbRoute)
    {{ Breadcrumbs::render($breadcrumbRoute) }}
@endif
