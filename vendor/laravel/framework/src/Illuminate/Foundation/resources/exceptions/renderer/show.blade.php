<x-laravel-exceptions-renderer::layout>
    <x-laravel-exceptions-renderer::section-container class="px-6 py-0 sm:py-0">
        <x-laravel-exceptions-renderer::topbar :title="$exception->title()" :markdown="$exceptionAsMarkdown" />
    </x-laravel-exceptions-renderer::section-container>

    <x-laravel-exceptions-renderer::separator />

    <x-laravel-exceptions-renderer::section-container class="flex flex-col gap-8 py-0 sm:py-0">
        <x-laravel-exceptions-renderer::header :$exception />
    </x-laravel-exceptions-renderer::section-container>

    <x-laravel-exceptions-renderer::separator class="-mt-5 -z-10" />

    <x-laravel-exceptions-renderer::section-container class="flex flex-col gap-8 pt-14">
        <x-laravel-exceptions-renderer::trace :$exception />

        <x-laravel-exceptions-renderer::query :queries="$exception->applicationQueries()" />
    </x-laravel-exceptions-renderer::section-container>

    <x-laravel-exceptions-renderer::separator />

    <x-laravel-exceptions-renderer::section-container class="flex flex-col gap-12">
        <x-laravel-exceptions-renderer::request-header :headers="$exception->requestHeaders()" />

        <x-laravel-exceptions-renderer::request-body :body="$exception->requestBody()" />

        <x-laravel-exceptions-renderer::routing :routing="$exception->applicationRouteContext()" />

        <x-laravel-exceptions-renderer::routing-parameter :routeParameters="$exception->applicationRouteParametersContext()" />
    </x-laravel-exceptions-renderer::section-container>

    <x-laravel-exceptions-renderer::separator />

    @if (! app()->runningUnitTests() && ! app()->runningInConsole())
        <x-laravel-exceptions-renderer::section-container class="pb-0 sm:pb-0">
            <x-laravel-exceptions-renderer::laravel-ascii-spotlight />
        </x-laravel-exceptions-renderer::section-container>
    @endif
</x-laravel-exceptions-renderer::layout>
