<?php

/**
 * Configurations for Backpack's ListOperation.
 *
 * @see https://backpackforlaravel.com/docs/crud-operation-list-entries
 */

return [
    // Define the size/looks of the content div for all CRUDs
    // To override per view use $this->crud->setListContentClass('class-string')
    'contentClass' => 'col-md-12',

    // enable the datatables-responsive plugin, which hides columns if they don't fit?
    // if not, a horizontal scrollbar will be shown instead
    'responsiveTable' => true,

    // stores pagination and filters in localStorage for two hours
    // whenever the user tries to see that page, backpack loads the previous pagination and filtration
    'persistentTable' => true,

    // show search bar in the top-right corner?
    'searchableTable' => true,

    // how much time should the system wait before triggering the search function after the user stops typing?
    'searchDelay' => 400,

    // the time the table will be persisted in minutes
    // after this the table info is cleared from localStorage.
    // use false to never force localStorage clear. (default)
    // keep in mind: User can clear their localStorage whenever they want.

    'persistentTableDuration' => false,

    // How many items should be shown by default by the Datatable?
    // This value can be overwritten on a specific CRUD by calling
    // $this->crud->setDefaultPageLength(50);
    'defaultPageLength' => 10,

    // A 1D array of options which will be used for both the displayed option and the value, or
    // A 2D array in which the first array is used to define the value options and the second array the displayed options
    // If a 2D array is used, strings in the right hand array will be automatically run through trans()
    'pageLengthMenu' => [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'backpack::crud.all']],

    // How important is it for the action buttons to be visible?
    // - 0 - most important
    // - 1 - as important as bulk buttons
    // - 2-3 - more important than the rest of the columns
    // - 4 - less important than most columns
    'actionsColumnPriority' => 1,

    // Nest action buttons within a dropdown in actions column
    'lineButtonsAsDropdown' => false,

    // What is the minimum actions for the dropdown to be created
    // Example: when minimum to drop is «2»,  any row with less than «2» action buttons
    // will not create a dropdown, but will show the buttons inline
    'lineButtonsAsDropdownMinimum' => 1,

    // Force «X» actions to be shown inline before the dropdown is created
    // Example: when setting this to «2», the first «2» actions will be shown inline
    // and the rest will be moved to the dropdown
    'lineButtonsAsDropdownShowBefore' => 0,

    // Show a "Reset" button next to the List operation subheading
    // (Showing 1 to 25 of 9999 entries. Reset)
    // that allows the user to erase local storage for that datatable,
    // thus clearing any searching, filtering or pagination that has been
    // remembered and persisted using persistentTable
    'resetButton' => true,

    // The query operator that is used to search on the table.
    // If you are using PostgreSQL you might want to change
    // to `ilike` for case-insensitive search
    'searchOperator' => 'like',

    // Display the `Showing X of XX entries (filtered  from X entries)`?
    // Setting this to false will improve performance on big datasets.
    'showEntryCount' => true,

    // when list operation load the information from database, should Backpack eager load the relations ?
    // this setting is enabled by default as it reduces the amount of queries required to load the page
    'eagerLoadRelationships' => true,
];
