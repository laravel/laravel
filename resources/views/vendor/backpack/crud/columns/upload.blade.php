@php
    $column['escaped'] = $column['escaped'] ?? false;
    $column['value'] = $column['value'] ?? data_get($entry, $column['name']);
    if(!empty($column['value'])) {
        $column['wrapper']['element'] = $column['wrapper']['element'] ?? 'a';
        $column['wrapper']['target'] = $column['wrapper']['target'] ?? '_blank';
        $column_wrapper_href = $column['wrapper']['href'] ?? function($file_path, $disk, $prefix) use ($column) { 
            if (is_null($disk)) {
                return asset($prefix.$file_path);
            }
            if (isset($column['temporary'])) {
                return asset(\Storage::disk($disk)->temporaryUrl($file_path, Carbon\Carbon::now()->addMinutes($column['temporary'])));
            }
            return asset(\Storage::disk($disk)->url($file_path)); 
        };
       
        $column['wrapper']['href'] = $column_wrapper_href instanceof \Closure ? $column_wrapper_href($column['value'], $column['disk'], $column['prefix'] ?? '') : $column_wrapper_href;
    }
@endphp
@include('crud::columns.text')
