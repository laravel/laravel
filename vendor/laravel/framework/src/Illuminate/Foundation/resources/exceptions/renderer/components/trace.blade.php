@props(['exception'])

<div class="flex flex-col gap-2.5 bg-neutral-50 dark:bg-white/1 border border-neutral-200 dark:border-neutral-800 rounded-xl p-2.5 shadow-xs">
    <div class="flex items-center gap-2.5 p-2">
        <div class="bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-white/5 rounded-md w-6 h-6 flex items-center justify-center p-1">
            <x-laravel-exceptions-renderer::icons.alert class="w-2.5 h-2.5 text-blue-500 dark:text-emerald-500" />
        </div>
        <h3 class="text-base font-semibold text-neutral-900 dark:text-white">Exception trace</h3>
    </div>

    <div class="flex flex-col gap-1.5">
        @foreach ($exception->frameGroups() as $group)
            @if ($group['is_vendor'])
                <x-laravel-exceptions-renderer::vendor-frames :frames="$group['frames']" />
            @else
                @foreach ($group['frames'] as $frame)
                    <x-laravel-exceptions-renderer::frame :$frame />
                @endforeach
            @endif
        @endforeach
    </div>
</div>
