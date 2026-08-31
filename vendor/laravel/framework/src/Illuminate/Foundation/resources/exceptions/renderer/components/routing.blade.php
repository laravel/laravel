@props(['routing'])

<div class="flex flex-col gap-3">
    <h2 class="text-lg font-semibold">Routing</h2>
    <div class="flex flex-col">
        @forelse ($routing as $key => $value)
        <div class="flex max-w-full items-baseline gap-2 h-10 text-sm font-mono">
            <div class="uppercase text-neutral-500 dark:text-neutral-400 shrink-0">{{ $key }}</div>
            <div class="min-w-6 grow h-3 border-b-2 border-dotted border-neutral-300 dark:border-white/20"></div>
            <div class="truncate text-neutral-900 dark:text-white">
                <span data-tippy-content="{{ $value }}">
                    {{ $value }}
                </span>
            </div>
        </div>
        @empty
        <x-laravel-exceptions-renderer::empty-state message="No routing context" />
        @endforelse
    </div>
</div>
