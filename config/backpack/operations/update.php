<?php

/**
 * Configurations for Backpack's UpdateOperation.
 *
 * @see https://backpackforlaravel.com/docs/crud-operation-update
 */

return [
    // Define the size/looks of the content div for all CRUDs
    // To override per view use $this->crud->setEditContentClass('class-string')
    'contentClass' => 'col-md-12 bold-labels',

    // When using tabbed forms (create & update), what kind of tabs would you like?
    'tabsType' => 'horizontal', //options: horizontal, vertical

    // How would you like the validation errors to be shown?
    'groupedErrors' => true,
    'inlineErrors' => true,

    // when the page loads, put the cursor on the first input?
    'autoFocusOnFirstField' => true,

    // Where do you want to redirect the user by default, save?
    // options: save_and_back, save_and_edit, save_and_new
    'defaultSaveAction' => 'save_and_back',

    // When the user chooses "save and back" or "save and new", show a bubble
    // for the fact that the default save action has been changed?
    'showSaveActionChange' => true, //options: true, false

    // Should we show a cancel button to the user?
    'showCancelButton' => true,

    // Should we show the delete button on the edit form?
    'showDeleteButton' => false,

    // Should we warn a user before leaving the page with unsaved changes?
    'warnBeforeLeaving' => false,

    // when viewing the update form of an entry in a language that's not translated should Backpack show a notice
    // that allows the user to fill the form from another language?
    'showTranslationNotice' => true,

    // when loading an update form, should Backpack eager load the relationship information from database?
    // this is generally a good thing to enable, as it helps to reduce the number of queries.
    'eagerLoadRelationships' => false,

    // Before saving the entry, how would you like the request to be stripped?
    //  - false - use Backpack's default (ONLY save inputs that have fields)
    //  - invokable class - custom stripping (the return should be an array with input names)
    // 'strippedRequest' => App\Http\Requests\StripBackpackRequest::class,
];
