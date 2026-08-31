@props(['frames'])

@use('Illuminate\Support\Str')

<div
    x-data="{ expanded: false }"
    class="group rounded-lg border border-neutral-200 dark:border-white/5"
    :class="{
        'bg-white dark:bg-white/5 shadow-xs': expanded,
        'border-dashed border-neutral-300 bg-neutral-50 opacity-90 dark:border-white/10 dark:bg-white/1': !expanded,
    }"
>
    <div
        class="flex h-11 cursor-pointer items-center gap-3 rounded-lg pr-2.5 pl-4 hover:bg-white/50 dark:hover:bg-white/2"
        @click="expanded = !expanded"
    >
        <x-laravel-exceptions-renderer::icons.folder class="w-3 h-3 text-neutral-400" x-show="!expanded" x-cloak />
        <x-laravel-exceptions-renderer::icons.folder-open class="w-3 h-3 text-blue-500 dark:text-emerald-500" x-show="expanded" />

        <div class="flex-1 font-mono text-xs leading-3 text-neutral-900 dark:text-neutral-400">
            {{ count($frames)}} vendor {{ Str::plural('frame', count($frames))}}
        </div>

        <button
            x-cloak
            type="button"
            class="flex h-6 w-6 cursor-pointer items-center justify-center rounded-md dark:border dark:border-white/8 group-hover:text-blue-500 group-hover:dark:text-emerald-500"
            :class="{
                'text-blue-500 dark:text-emerald-500 dark:bg-white/5': expanded,
                'text-neutral-500 dark:text-neutral-500 dark:bg-white/3': !expanded,
            }"
        >
            <x-laravel-exceptions-renderer::icons.chevrons-down-up x-show="expanded" />
            <x-laravel-exceptions-renderer::icons.chevrons-up-down x-show="!expanded" />
        </button>
    </div>

    <div x-cloak class="flex flex-col rounded-b-lg divide-y divide-neutral-200 border-t border-neutral-200 dark:divide-white/5 dark:border-white/5" x-show="expanded">
        @foreach ($frames as $frame)
            <div class="flex flex-col divide-y divide-neutral-200 dark:divide-white/5">
                <x-laravel-exceptions-renderer::vendor-frame :$frame />
            </div>
        @endforeach
    </div>
</div>
