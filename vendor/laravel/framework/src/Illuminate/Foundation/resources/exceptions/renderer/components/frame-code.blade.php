@props(['code', 'highlightedLine'])

<div
    class="text-sm rounded-b-lg bg-neutral-50 border-t border-neutral-100 dark:bg-neutral-900 dark:border-white/10"
    {{ $attributes }}
>
    <x-laravel-exceptions-renderer::syntax-highlight
        :code="$code"
        language="php"
        editor
        :starting-line="max(1, $highlightedLine - 5)"
        :highlighted-line="min(5, $highlightedLine - 1)"
        class="overflow-x-auto"
    />
</div>
