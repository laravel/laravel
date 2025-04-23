@if(count($columns))
    <table class="table table-striped m-0 p-0">
        <tbody>
        @foreach($columns as $column)
            <tr>
                <td @if($loop->index === 0) class="border-top-0" @endif>
                    <strong>{!! $column['label'] !!}@if(!empty($column['label'])):@endif</strong>
                </td>
                <td @if($loop->index === 0) class="border-top-0" @endif>
                    @php
                        // create a list of paths to column blade views
                        // including the configured view_namespaces
                        $columnPaths = array_map(function($item) use ($column) {
                            return $item.'.'.$column['type'];
                        }, \Backpack\CRUD\ViewNamespaces::getFor('columns'));

                        // but always fall back to the stock 'text' column
                        // if a view doesn't exist
                        if (!in_array('crud::columns.text', $columnPaths)) {
                            $columnPaths[] = 'crud::columns.text';
                        }
                    @endphp
                    @includeFirst($columnPaths)
                </td>
            </tr>
        @endforeach
        @if($crud->buttons()->where('stack', 'line')->count() && ($displayActionsColumn ?? true))
            <tr>
                <td>
                    <strong>{{ trans('backpack::crud.actions') }}</strong>
                </td>
                <td>
                    @include('crud::inc.button_stack', ['stack' => 'line'])
                </td>
            </tr>
        @endif
        </tbody>
    </table>
@endif
