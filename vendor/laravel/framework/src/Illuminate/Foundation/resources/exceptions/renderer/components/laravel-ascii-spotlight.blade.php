<div
    class="relative text-neutral-400 dark:text-neutral-400"
    x-data="{ spotlight: { x: 0, y: 0 } }"
    @mousemove="const rect = $el.getBoundingClientRect(); spotlight = { x: $event.clientX - rect.left, y: $event.clientY - rect.top }">
    <div
        class="absolute w-full text-neutral-800 dark:text-neutral-100"
        x-data="{ isDark: window.matchMedia('(prefers-color-scheme: dark)').matches || document.documentElement.classList.contains('dark') }"
        :style="
            'mask-image: radial-gradient(circle at ' +
                spotlight.x +
                'px ' +
                spotlight.y +
                'px, black 0%, transparent ' + (isDark ? '150px' : '120px') + '); -webkit-mask-image: radial-gradient(circle at ' +
                spotlight.x +
                'px ' +
                spotlight.y +
                'px, black 0%, transparent ' + (isDark ? '600px' : '400px') + ');'
        ">
        <x-laravel-exceptions-renderer::icons.laravel-ascii />
    </div>
    <x-laravel-exceptions-renderer::icons.laravel-ascii />
</div>
