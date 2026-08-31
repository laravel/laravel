@props(['queries'])

<div
    {{ $attributes->merge(['class' => "flex flex-col gap-2.5 bg-neutral-50 dark:bg-white/1 border border-neutral-200 dark:border-neutral-800 rounded-xl p-2.5 shadow-xs"]) }}
    x-data="{
        totalQueries: {{ min(count($queries), 100) }},
        currentPage: 1,
        perPage: 10,
        get totalPages() {
            return Math.ceil(this.totalQueries / this.perPage);
        },
        get hasPrevious() {
            return this.currentPage > 1;
        },
        get hasNext() {
            return this.currentPage < this.totalPages;
        },
        goToPage(page) {
            if (page >= 1 && page <= this.totalPages) {
                this.currentPage = page;
            }
        },
        first() {
            this.currentPage = 1;
        },
        last() {
            this.currentPage = this.totalPages;
        },
        previous() {
            if (this.hasPrevious) {
                this.currentPage--;
            }
        },
        next() {
            if (this.hasNext) {
                this.currentPage++;
            }
        },
        get visiblePages() {
            const total = this.totalPages;
            const current = this.currentPage;
            const pages = [];

            if (total <= 7) {
                for (let i = 1; i <= total; i++) {
                    pages.push({ type: 'page', value: i });
                }
            } else {
                if (current <= 4) {
                    for (let i = 1; i <= 5; i++) {
                        pages.push({ type: 'page', value: i });
                    }
                    if (total > 6) {
                        pages.push({ type: 'ellipsis', value: '...', id: 'end' });
                        pages.push({ type: 'page', value: total });
                    }
                } else if (current > total - 4) {
                    pages.push({ type: 'page', value: 1 });
                    if (total > 6) {
                        pages.push({ type: 'ellipsis', value: '...', id: 'start' });
                    }
                    for (let i = Math.max(total - 4, 2); i <= total; i++) {
                        pages.push({ type: 'page', value: i });
                    }
                } else {
                    pages.push({ type: 'page', value: 1 });
                    pages.push({ type: 'ellipsis', value: '...', id: 'start' });
                    for (let i = current - 1; i <= current + 1; i++) {
                        pages.push({ type: 'page', value: i });
                    }
                    pages.push({ type: 'ellipsis', value: '...', id: 'end' });
                    pages.push({ type: 'page', value: total });
                }
            }
            return pages;
        }
    }"
>
    <div class="flex items-center justify-between p-2">
        <div class="flex items-center gap-2.5">
            <div class="bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-white/5 rounded-md w-6 h-6 flex items-center justify-center p-1">
                <x-laravel-exceptions-renderer::icons.database class="w-2.5 h-2.5 text-blue-500 dark:text-emerald-500" />
            </div>
            <h3 class="text-base font-semibold">Queries</h3>
        </div>
        <div x-show="totalQueries > 0" class="text-sm text-neutral-500 dark:text-neutral-400 flex items-center gap-2">
            <span x-text="`${((currentPage - 1) * perPage) + 1}-${Math.min(currentPage * perPage, totalQueries)} of ${totalQueries}`"></span>
            @if (count($queries) > 100)
                <x-laravel-exceptions-renderer::icons.info
                    class="w-3 h-3 text-blue-500 dark:text-emerald-500"
                    data-tippy-content="Only the first 100 queries are shown"
                />
            @endif
        </div>
    </div>

    <div class="flex flex-col gap-1">
        @forelse (array_slice($queries, 0, 100) as $index => ['connectionName' => $connectionName, 'sql' => $sql, 'time' => $time])
        <div
            class="border border-neutral-200 dark:border-none bg-white dark:bg-white/[3%] rounded-md h-10 flex items-center justify-between gap-4 px-4 text-xs font-mono shadow-xs"
            x-show="Math.floor({{ $index }} / perPage) === (currentPage - 1)"
        >
            <div class="flex items-center gap-2 truncate">
                <div class="flex items-center gap-2">
                    <x-laravel-exceptions-renderer::icons.database class="w-3 h-3 text-neutral-500 dark:text-neutral-400" />
                    <span class="text-neutral-500 dark:text-neutral-400">{{ $connectionName }}</span>
                </div>
                <x-laravel-exceptions-renderer::syntax-highlight
                    :code="$sql"
                    language="sql"
                    truncate
                    class="min-w-0"
                    data-tippy-content="{{ nl2br($sql) }}"
                />
            </div>
            <div class="text-neutral-500 dark:text-neutral-200 text-right flex-shrink-0">{{ $time }}ms</div>
        </div>
        @empty
        <x-laravel-exceptions-renderer::empty-state message="No queries executed" />
        @endforelse
    </div>

    <!-- Pagination Controls -->
    <div x-cloak x-show="totalPages > 1" class="flex items-center justify-center gap-1 py-4 font-mono">
        <!-- First Button -->
        <button
            @click="first()"
            class="cursor-pointer flex items-center justify-center w-8 h-8 rounded-md transition-colors"
            :disabled="!hasPrevious"
            :class="hasPrevious ? 'text-neutral-500 dark:text-neutral-300 hover:bg-neutral-200 hover:dark:text-white hover:dark:bg-white/5' : 'text-neutral-600 cursor-not-allowed!'"
        >
            <x-laravel-exceptions-renderer::icons.chevrons-left class="w-3 h-3" />
        </button>

        <!-- Previous Button -->
        <button
            @click="previous()"
            class="cursor-pointer flex items-center justify-center w-8 h-8 rounded-md transition-colors"
            :class="hasPrevious ? 'text-neutral-500 dark:text-neutral-300 hover:bg-neutral-200 hover:dark:text-white hover:dark:bg-white/5' : 'text-neutral-600 cursor-not-allowed!'"
            :disabled="!hasPrevious"
        >
            <x-laravel-exceptions-renderer::icons.chevron-left class="w-3 h-3" />
        </button>

        <!-- Page Numbers -->
        <template x-for="(page, index) in visiblePages" :key="`page-${page.type}-${page.value}-${page.id || index}`">
            <div>
                <template x-if="page.type === 'ellipsis'">
                    <span class="flex items-center justify-center w-8 h-8 text-neutral-500">...</span>
                </template>
                <template x-if="page.type === 'page'">
                    <button
                        @click="goToPage(page.value)"
                        class="cursor-pointer flex items-center justify-center w-8 h-8 rounded-md text-sm font-medium transition-colors"
                        :class="currentPage === page.value ? 'bg-blue-600 text-white' : 'text-neutral-500 dark:text-neutral-300 hover:bg-neutral-200 hover:dark:text-white hover:dark:bg-white/5'"
                        x-text="page.value"
                    ></button>
                </template>
            </div>
        </template>

        <!-- Next Button -->
        <button
            @click="next()"
            class="cursor-pointer flex items-center justify-center w-8 h-8 rounded-md transition-colors"
            :class="hasNext ? 'text-neutral-500 dark:text-neutral-300 hover:bg-neutral-200 hover:dark:text-white hover:dark:bg-white/5' : 'text-neutral-600 cursor-not-allowed!'"
            :disabled="!hasNext"
        >
            <x-laravel-exceptions-renderer::icons.chevron-right class="w-3 h-3" />
        </button>

        <!-- Last Button -->
        <button
            @click="last()"
            class="cursor-pointer flex items-center justify-center w-8 h-8 rounded-md transition-colors"
            :class="hasNext ? 'text-neutral-500 dark:text-neutral-300 hover:bg-neutral-200 hover:dark:text-white hover:dark:bg-white/5' : 'text-neutral-600 cursor-not-allowed!'"
            :disabled="!hasNext"
        >
            <x-laravel-exceptions-renderer::icons.chevrons-right class="w-3 h-3" />
        </button>
    </div>
</div>
