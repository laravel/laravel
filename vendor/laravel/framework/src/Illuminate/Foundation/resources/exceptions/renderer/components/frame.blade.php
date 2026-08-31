@props(['frame'])

<div
    x-data="{
        expanded: {{ $frame->isMain() ? 'true' : 'false' }},
        hasCode: {{ $frame->snippet() ? 'true' : 'false' }}
    }"
    class="group rounded-lg border border-neutral-200 dark:border-white/10 overflow-hidden shadow-xs"
    :class="{ 'dark:border-white/5': expanded }"
>
    <div
        class="flex h-11 items-center gap-3 bg-white pr-2.5 pl-4 overflow-x-auto dark:bg-white/3"
        :class="{
            'cursor-pointer hover:bg-white/50 dark:hover:bg-white/5 hover:[&_svg]:stroke-emerald-500': hasCode,
            'dark:bg-white/5 rounded-t-lg': expanded,
            'dark:bg-white/3 rounded-lg': !expanded
        }"
        @click="hasCode && (expanded = !expanded)"
    >
        {{-- Dot --}}
        <div class="flex size-3 items-center justify-center flex-shrink-0">
          <div
          class="size-2 rounded-full"
          :class="{
            'bg-rose-500 dark:bg-neutral-400': expanded,
            'bg-rose-200 dark:bg-neutral-700': !expanded
          }"
          ></div>
        </div>

        <div class="flex flex-1 items-center justify-between gap-6 min-w-0">
            <x-laravel-exceptions-renderer::formatted-source :$frame />
            <x-laravel-exceptions-renderer::file-with-line :$frame direction="rtl" />
        </div>

        <div class="flex-shrink-0">
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
    </div>

    @if($snippet = $frame->snippet())
        <x-laravel-exceptions-renderer::frame-code :code="$snippet" :highlightedLine="$frame->line()" x-show="expanded" :x-cloak="!$frame->isMain()" />
    @endif
</div>
