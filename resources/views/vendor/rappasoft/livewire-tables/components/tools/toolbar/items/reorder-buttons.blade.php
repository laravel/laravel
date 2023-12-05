@aware(['component', 'tableName'])
<div x-data x-cloak x-show="reorderStatus"
    @class([
        'mr-0 mr-md-2 mb-3 mb-md-0' => $component->isBootstrap4(),
        'me-0 me-md-2 mb-3 mb-md-0' => $component->isBootstrap5()
    ])
>
    <button
        x-on:click="reorderToggle"
        type="button"
        @class([
            'btn btn-default d-block d-md-inline' => $component->isBootstrap(),
            'inline-flex justify-center items-center w-full md:w-auto px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 active:bg-gray-50 active:text-gray-800 transition ease-in-out duration-150 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600' => $component->isTailwind()
        ])
    >
        <span x-cloak x-show="currentlyReorderingStatus">
            @lang('Cancel')
        </span>

        <span x-cloak x-show="!currentlyReorderingStatus">
            @lang('Reorder')
        </span>

    </button>
    
    <button
        type="button"
        x-cloak x-show="currentlyReorderingStatus" 
        x-on:click="updateOrderedItems"
        @class([
            'btn btn-default d-block d-md-inline' => $component->isBootstrap() && $component->currentlyReorderingStatus,
            'inline-flex justify-center items-center w-full md:w-auto px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 active:bg-gray-50 active:text-gray-800 transition ease-in-out duration-150 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600' => $component->isTailwind()
        ])
    >
        <span>
            @lang('Save')
        </span>
    </button>

</div>
