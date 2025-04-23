<?php

/**
 * Configurations for Backpack ReorderOperation.
 *
 * @see https://backpackforlaravel.com/docs/crud-operation-reorder
 */

return [
    // Define the size/looks of the content div for all CRUDs
    // To override per Controller use $this->crud->setReorderContentClass('class-string')
    'contentClass' => 'col-md-12 col-md-offset-2',

    // should the content of the reorder label be escaped?
    'escaped' => false,
];
