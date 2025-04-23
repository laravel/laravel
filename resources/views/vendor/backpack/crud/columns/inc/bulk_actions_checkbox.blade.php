@if (!isset($entry))
    <span class="crud_bulk_actions_checkbox">
        <input type="checkbox" class="crud_bulk_actions_general_checkbox form-check-input">
    </span>
@else
    <span class="crud_bulk_actions_checkbox">
        <input type="checkbox" class="crud_bulk_actions_line_checkbox form-check-input" data-primary-key-value="{{ $entry->getKey() }}">
    </span>

    @bassetBlock('backpack/crud/operations/list/bulk-actions-checkbox.js')
    <script>
    if (typeof addOrRemoveCrudCheckedItem !== 'function') {
        function addOrRemoveCrudCheckedItem(element) {
            crud.lastCheckedItem = false;

            document.querySelectorAll('input.crud_bulk_actions_line_checkbox').forEach(checkbox => checkbox.onclick = e => {
                e.stopPropagation();

                let checked = checkbox.checked;
                let primaryKeyValue = checkbox.dataset.primaryKeyValue;

                crud.checkedItems ??= [];
                
                if (checked) {
                    // add item to crud.checkedItems variable
                    crud.checkedItems.push(primaryKeyValue);

                    // if shift has been pressed, also select all elements
                    // between the last checked item and this one
                    if (crud.lastCheckedItem && e.shiftKey) {
                        let getNodeindex = elm => [...elm.parentNode.children].indexOf(elm);
                        let first = document.querySelector(`input.crud_bulk_actions_line_checkbox[data-primary-key-value="${crud.lastCheckedItem}"]`).closest('tr');
                        let last = document.querySelector(`input.crud_bulk_actions_line_checkbox[data-primary-key-value="${primaryKeyValue}"]`).closest('tr');
                        let firstIndex = getNodeindex(first);
                        let lastIndex = getNodeindex(last)
                        
                        while(first !== last) {
                            first = firstIndex < lastIndex ? first.nextElementSibling : first.previousElementSibling;
                            first.querySelector('input.crud_bulk_actions_line_checkbox:not(:checked)')?.click();
                        }
                    }

                    // remember that this one was the last checked item
                    crud.lastCheckedItem = primaryKeyValue;
                } else {
                    // remove item from crud.checkedItems variable
                    let index = crud.checkedItems.indexOf(primaryKeyValue);
                    if (index > -1) crud.checkedItems.splice(index, 1);
                }

                // if no items are selected, disable all bulk buttons
                enableOrDisableBulkButtons();
            });
        }
    }

    if (typeof markCheckboxAsCheckedIfPreviouslySelected !== 'function') {
        function markCheckboxAsCheckedIfPreviouslySelected() {
            let checkedItems = crud.checkedItems ?? [];
            let pageChanged = localStorage.getItem('page_changed') ?? false;
            let tableUrl = crud.table.ajax.url();
            let hasFilterApplied = false;

            if (tableUrl.indexOf('?') > -1) {
                if (tableUrl.substring(tableUrl.indexOf('?') + 1).length > 0) {
                    hasFilterApplied = true;
                }
            }

            // if it was not a page change, we check if datatables have any search, or the url have any parameters.
            // if you have filtered entries, and then remove the filters we are sure the entries are in the table.
            // we don't remove them in that case.
            if (! pageChanged && (crud.table.search().length !== 0 || hasFilterApplied)) {
                crud.checkedItems = [];
            }
            document
                .querySelectorAll('input.crud_bulk_actions_line_checkbox[data-primary-key-value]')
                .forEach(function(elem) {
                    let checked = checkedItems.length && checkedItems.indexOf(elem.dataset.primaryKeyValue) > -1;
                    elem.checked = checked;
                    if (checked && crud.checkedItems.indexOf(elem.dataset.primaryKeyValue) === -1) {
                        crud.checkedItems.push(elem.dataset.primaryKeyValue);
                    }
                });
            
            localStorage.removeItem('page_changed');
        }
    }

    if (typeof addBulkActionMainCheckboxesFunctionality !== 'function') {
        function addBulkActionMainCheckboxesFunctionality() {
            let mainCheckboxes = Array.from(document.querySelectorAll('input.crud_bulk_actions_general_checkbox'));
            let rowCheckboxes = Array.from(document.querySelectorAll('input.crud_bulk_actions_line_checkbox'));

            mainCheckboxes.forEach(checkbox => {
                // set initial checked status
                checkbox.checked = document.querySelectorAll('input.crud_bulk_actions_line_checkbox:not(:checked)').length === 0;

                // when the crud_bulk_actions_general_checkbox is selected, toggle all visible checkboxes
                checkbox.onclick = event => {
                    rowCheckboxes.filter(elem => checkbox.checked !== elem.checked).forEach(elem => elem.click());
                    
                    // make sure the other checkbox has the same checked status
                    mainCheckboxes.forEach(elem => elem.checked = checkbox.checked);

                    event.stopPropagation();
                }
            });

            // Stop propagation of href on the first column
            document.querySelectorAll('table td.dtr-control a').forEach(link => link.onclick = e => e.stopPropagation());
        }
    }

    if (typeof enableOrDisableBulkButtons !== 'function') {
        function enableOrDisableBulkButtons() {
            document.querySelectorAll('.bulk-button').forEach(btn => btn.classList.toggle('disabled', !crud.checkedItems?.length));
        }
    }

    crud.addFunctionToDataTablesDrawEventQueue('addOrRemoveCrudCheckedItem');
    crud.addFunctionToDataTablesDrawEventQueue('markCheckboxAsCheckedIfPreviouslySelected');
    crud.addFunctionToDataTablesDrawEventQueue('addBulkActionMainCheckboxesFunctionality');
    crud.addFunctionToDataTablesDrawEventQueue('enableOrDisableBulkButtons');
    </script>
    @endBassetBlock
@endif
