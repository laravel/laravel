@props(['exception', 'request'])

<div
    x-data="{
        copied: false,
        async copyToClipboard() {
            try {
                await window.copyToClipboard('{{ $request->fullUrl() }}');
                this.copied = true;
                setTimeout(() => { this.copied = false }, 3000);
            } catch (err) {
                console.error('Failed to copy the requestURL: ', err);
            }
        }
    }"
    {{ $attributes->merge(['class' => "bg-white dark:bg-[#1a1a1a] border border-neutral-200 dark:border-white/10 rounded-lg flex items-center justify-between h-10 px-2 shadow-xs"]) }}
>
    <div class="flex items-center gap-3 w-full">
        <x-laravel-exceptions-renderer::badge type="error" variant="solid">
            <x-laravel-exceptions-renderer::icons.alert class="w-2.5 h-2.5" />
            {{ $exception->httpStatusCode() }}
        </x-laravel-exceptions-renderer::badge>
        <x-laravel-exceptions-renderer::http-method method="{{ $request->method() }}" />
        <div class="flex-1 text-sm font-light truncate text-neutral-950 dark:text-white">
            <span data-tippy-content="{{ $request->fullUrl() }}">
                {{ $request->fullUrl() }}
            </span>
        </div>
        <button
            x-cloak
            @click="copyToClipboard()"
            @class([
                "rounded-md w-6 h-6 flex flex-shrink-0 items-center justify-center cursor-pointer border transition-colors duration-200 ease-in-out",
                "bg-white/5 border-neutral-200 hover:bg-neutral-100 dark:bg-white/5 dark:border-white/10 dark:hover:bg-white/10",
            ])
        >
            <x-laravel-exceptions-renderer::icons.copy class="w-3 h-3 text-neutral-400" x-show="!copied" />
            <x-laravel-exceptions-renderer::icons.check class="w-3 h-3 text-emerald-500" x-show="copied" />
        </button>
    </div>
</div>
