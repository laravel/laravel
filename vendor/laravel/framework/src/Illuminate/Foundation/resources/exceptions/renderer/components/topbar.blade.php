@props(['title', 'markdown'])

<script>
    const markdown = {{ Illuminate\Support\Js::from($markdown) }}
</script>

<div
    class="flex items-center justify-between"
    x-data="{
        copied: false,
        async copyToClipboard() {
            try {
                await window.copyToClipboard(markdown);
                this.copied = true;
                setTimeout(() => { this.copied = false }, 3000);
            } catch (err) {
                console.error('Failed to copy the markdown: ', err);
            }
        }
    }"
>
    <div class="flex items-center gap-2 h-[56px]">
        <div class="w-[18px] h-[18px] flex items-center justify-center bg-rose-500 rounded-md">
            <svg width="2" height="10" class="text-white" viewBox="0 0 2 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1.00006 6.3188C1.41416 6.3188 1.75006 5.98295 1.75006 5.56885V1.43115C1.75006 1.01705 1.41416 0.681152 1.00006 0.681152C0.585961 0.681152 0.250061 1.01705 0.250061 1.43115V5.56885C0.250061 5.98295 0.585961 6.3188 1.00006 6.3188Z" fill="currentColor" />
                <path d="M1.00006 9.41699C1.55235 9.41699 2.00007 8.96929 2.00007 8.41699C2.00007 7.86469 1.55235 7.41699 1.00006 7.41699C0.447781 7.41699 6.10352e-05 7.86469 6.10352e-05 8.41699C6.10352e-05 8.96929 0.447781 9.41699 1.00006 9.41699Z" fill="currentColor "/>
            </svg>
        </div>
        <div class="font-medium text-sm text-neutral-900 dark:text-white">
            {{ $title }}
        </div>
    </div>

    <button
        x-cloak
        @class([
            "text-sm rounded-md border px-3 h-8 flex items-center gap-2 transition-colors duration-200 ease-in-out cursor-pointer shadow-xs",
            "text-neutral-600 dark:text-neutral-400 bg-white/5 border-neutral-200 hover:bg-neutral-100 dark:bg-white/5 dark:border-white/10 dark:hover:bg-white/10",
        ])
        @click="copyToClipboard()"
    >
        <x-laravel-exceptions-renderer::icons.copy class="w-3 h-3" x-show="!copied" />
        <x-laravel-exceptions-renderer::icons.check class="w-3 h-3 text-emerald-500" x-show="copied" />
        <span x-text="copied ? 'Copied to clipboard' : 'Copy as Markdown'"></span>
    </button>
</div>
