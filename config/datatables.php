<?php

return [
    /*
     * DataTables search options.
     */
    'search' => [
        /*
         * Smart search will enclose search keyword with wildcard string "%keyword%".
         * SQL: column LIKE "%keyword%"
         */
        'smart' => true,

        /*
         * Multi-term search will explode search keyword using spaces resulting into multiple term search.
         */
        'multi_term' => true,

        /*
         * Case insensitive will search the keyword in lower case format.
         * SQL: LOWER(column) LIKE LOWER(keyword)
         */
        'case_insensitive' => true,

        /*
         * Wild card will add "%" in between every characters of the keyword.
         * SQL: column LIKE "%k%e%y%w%o%r%d%"
         */
        'use_wildcards' => false,
    ],

    /*
     * DataTables internal index id response column name.
     */
    'index_column' => 'DT_Row_Index',

    /*
     * List of available builders for DataTables.
     * This is where you can register your custom dataTables builder.
     */
    'engines' => [
        'eloquent'   => \Yajra\DataTables\EloquentDataTable::class,
        'query'      => \Yajra\DataTables\QueryDataTable::class,
        'collection' => \Yajra\DataTables\CollectionDataTable::class,
    ],

    /*
     * DataTables accepted builder to engine mapping.
     * This is where you can override which engine a builder should use
     * Note, only change this if you know what you are doing!
     */
    'builders' => [
        //Illuminate\Database\Eloquent\Relations\Relation::class => 'eloquent',
        //Illuminate\Database\Eloquent\Builder::class            => 'eloquent',
        //Illuminate\Database\Query\Builder::class               => 'query',
        //Illuminate\Support\Collection::class                   => 'collection',
    ],

    /*
     * Nulls last sql pattern for Posgresql & Oracle.
     * For MySQL, use '-%s %s'
     */
    'nulls_last_sql' => '%s %s NULLS LAST',

    /*
     * User friendly message to be displayed on user if error occurs.
     * Possible values:
     * null             - The exception message will be used on error response.
     * 'throw'          - Throws a \Yajra\DataTables\Exceptions\Exception. Use your custom error handler if needed.
     * 'custom message' - Any friendly message to be displayed to the user. You can also use translation key.
     */
    'error' => env('DATATABLES_ERROR', null),

    /*
     * Default columns definition of dataTable utility functions.
     */
    'columns' => [
        /*
         * List of columns hidden/removed on json response.
         */
        'excess' => ['rn', 'row_num'],

        /*
         * List of columns to be escaped. If set to *, all columns are escape.
         * Note: You can set the value to empty array to disable XSS protection.
         */
        'escape' => '*',

        /*
         * List of columns that are allowed to display html content.
         * Note: Adding columns to list will make us available to XSS attacks.
         */
        'raw' => ['action'],

        /*
         * List of columns are are forbidden from being searched/sorted.
         */
        'blacklist' => ['password', 'remember_token'],

        /*
         * List of columns that are only allowed fo search/sort.
         * If set to *, all columns are allowed.
         */
        'whitelist' => '*',
    ],

    /*
     * JsonResponse header and options config.
     */
    'json' => [
        'header'  => [],
        'options' => 0,
    ],

];
