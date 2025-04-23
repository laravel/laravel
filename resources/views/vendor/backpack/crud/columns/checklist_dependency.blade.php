
@php
    // since this field is sensible to have long list, for example a list of permissions, we enforce
    // a maximum column width in px so that we avoid taking up the whole table space.
    $column['maxWidth'] = $column['maxWidth'] ?? 400;
    
    $primaryDependency = $column['subfields']['primary'];
    $secondaryDependency = $column['subfields']['secondary'];
    $secondaryDependency['options'] = $secondaryDependency['value'] = [];

    // Primary dependency field is the direct relation with the entry, so a select multiple is the way to go.
    $primaryDependency['type'] = 'select_multiple';
    // The second dependency we build from the items selected in the first dependency plus the direct entry associations.
    // Having all the information on our side we can use the select from array and give the desired options/values to show.
    $secondaryDependency['type'] = 'select_from_array';

    // Get the primary and secondary dependencies directly associated with entry.
    $primaryDependencies = $entry->{$primaryDependency['entity']} ?? [];
    $secondaryDependencies = $entry->{$secondaryDependency['entity']} ?? [];

    $secondaryDependencyOptions = [];
    // Loop the primary dependencies to get related secondary dependencies and add them as options for the select.
    foreach ($primaryDependencies as $primary) {
        foreach ($primary->{$secondaryDependency['entity']} as $secondary) {
            $secondaryDependencyOptions[] = $secondary->{$secondaryDependency['attribute']};
        }
    }

    // Merge the direct secondary dependencies items with secondary dependencies from primary
    foreach($secondaryDependencies as $secondary) {
        $secondaryDependencyOptions[] = $secondary->{$secondaryDependency['attribute']};
    }

    $secondaryDependency['options'] = $secondaryDependency['value'] = array_unique($secondaryDependencyOptions);
@endphp

<div style="max-width: {{$column['maxWidth']}}px; white-space: normal;">
    <b>{!!$column['subfields']['primary']['label'] !!}: &nbsp;</b> {!! $crud->getCellView($primaryDependency, $entry) !!} <br/>
    <b>{!!$column['subfields']['secondary']['label'] !!}: &nbsp;</b> {!! $crud->getCellView($secondaryDependency, $entry) !!}
</div>
