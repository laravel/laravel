<?php

return [
    /*
     * Namespaces used by the generator.
     */
    'namespace' => [
        /*
         * Base namespace/directory to create the new file.
         * This is appended on default Laravel namespace.
         * Usage: php artisan datatables:make User
         * Output: App\DataTables\UserDataTable
         * With Model: App\User (default model)
         * Export filename: users_timestamp
         */
        'base' => 'DataTables',

        /*
         * Base namespace/directory where your model's are located.
         * This is appended on default Laravel namespace.
         * Usage: php artisan datatables:make Post --model
         * Output: App\DataTables\PostDataTable
         * With Model: App\Post
         * Export filename: posts_timestamp
         */
        'model' => 'App\\Models',
    ],

    /*
     * Set Custom stub folder
     */
    //'stub' => '/resources/custom_stub',

    /*
     * PDF generator to be used when converting the table to pdf.
     * Available generators: excel, snappy
     * Snappy package: barryvdh/laravel-snappy
     * Excel package: maatwebsite/excel
     */
    'pdf_generator' => 'snappy',

    /*
     * Snappy PDF options.
     */
    'snappy' => [
        'options' => [
            'no-outline' => true,
            'margin-left' => '0',
            'margin-right' => '0',
            'margin-top' => '10mm',
            'margin-bottom' => '10mm',
        ],
        'orientation' => 'landscape',
    ],

    /*
     * Default html builder parameters.
     */
    'parameters' => [
        'dom'     => '<\'row mb-4\'<\'col-xxl-2 col-xl-3 col-lg-6 col-4 my-auto\'l><\'col-xxl-7 col-xl-6 col-lg-6 col-4 my-auto text-xl-center text-end \'B><\'col-xxl-3 col-xl-3 col-lg-12 col-4 my-auto\'f>>rt<\'row\'<\'col\'i><\'col text-right\'p>>',
        'stateSave' => true,
        'buttons' => [
            'csv',
            'print',
            'reset',
            'reload',
        ],
    ],

    /*
     * Generator command default options value.
     */
    'generator' => [
        /*
         * Default columns to generate when not set.
         */
        'columns' => 'id,created_at,updated_at',

        /*
         * Default buttons to generate when not set.
         */
        'buttons' => 'csv,print,reset,reload',

        /*
         * Default DOM to generate when not set.
         */
        'dom' => '<\'row mb-4\'<\'col-xxl-2 col-xl-3 col-lg-6 col-4 my-auto\'l><\'col-xxl-7 col-xl-6 col-lg-6 col-4 my-auto text-xl-center text-end \'B><\'col-xxl-3 col-xl-3 col-lg-12 col-4 my-auto\'f>>rt<\'row\'<\'col\'i><\'col text-right\'p>>',
    ],
];
