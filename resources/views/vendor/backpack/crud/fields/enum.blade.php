{{-- enum --}}
@php
    $entity_model = $field['model'] ?? $field['baseModel'] ?? $crud->model;

    $field['value'] = old_empty_or_null($field['name'], '') ??  $field['value'] ?? $field['default'] ?? '';

    $allowMultipleValues = $field['multiple'] ?? false;

    $possible_values = (function() use ($entity_model, $field, &$allowMultipleValues) {
        $fieldName = $field['baseFieldName'] ?? $field['name'];
        // if developer provided the options, use them, no need to guess.
        if(isset($field['options'])) {
            return $field['options'];
        }

        // if we are in a PHP version where PHP enums are not available, it can only be a database enum
        if(! function_exists('enum_exists')) {
            $options = $entity_model::getPossibleEnumValues($fieldName);
            return array_combine($options, $options);
        }
        
        // developer can provide the enum class so that we extract the available options from it
        $enumClassReflection = isset($field['enum_class']) ? new \ReflectionEnum($field['enum_class']) : false;

        if(! $enumClassReflection) {
            // check for model casting
            $possibleEnumCast = (new $entity_model)->getCasts()[$fieldName] ?? false;

            if($possibleEnumCast) {
                if(class_exists($possibleEnumCast)) {
                    $enumClassReflection = new \ReflectionEnum($possibleEnumCast);
                }else{
                    // It's an `AsEnumCollection` cast
                    if(str_contains($possibleEnumCast, ':')) {
                        $enumClassReflection = new \ReflectionEnum(explode(':', $possibleEnumCast)[1]);
                        $allowMultipleValues = true;
                    }   
                }
            }
        }

        if($enumClassReflection) {
            $options = array_map(function($item) use ($enumClassReflection) {
                return $enumClassReflection->isBacked() ? [$item->getBackingValue() => $item->name] : $item->name;
            },$enumClassReflection->getCases());
            $options = is_multidimensional_array($options) ? array_replace(...$options) : array_combine($options, $options);
        }

        if(isset($field['enum_function']) && isset($options)) {
            $options = array_map(function($item) use ($field, $enumClassReflection) {
                if ($enumClassReflection->hasConstant($item)) {
                    return $enumClassReflection->getConstant($item)->{$field['enum_function']}();
                }
                return $item;
            }, $options);
            return $options;
        }

        // if we have the enum options return them
        if(isset($options)) {
            return $options;
        }

        // no enum options, can only be database enum
        $options = $entity_model::getPossibleEnumValues($field['name']);
        return array_combine($options, $options);
    })();

    if(function_exists('enum_exists') && !empty($field['value']))  {
        match(true) {
            $field['value'] instanceof \UnitEnum => $field['value'] = $field['value'] instanceof \BackedEnum ? $field['value']->value : $field['value']->name,
            $field['value'] instanceof Illuminate\Support\Collection => $field['value'] = $field['value']->map(function($item) {
                return $item instanceof \UnitEnum ? $item instanceof \BackedEnum ? $item->value : $item->name : $item;
            })->toArray(),
            default => null
        };
    }
    $field['value'] = is_array($field['value']) ? $field['value'] : (!empty($field['value']) ? [$field['value']] : []);

@endphp

@include('crud::fields.inc.wrapper_start')
    <label>{!! $field['label'] !!}</label>
    @include('crud::fields.inc.translatable_icon')
    <select
        name="{{ $allowMultipleValues ? $field['name'].'[]' : $field['name'] }}"
        @include('crud::fields.inc.attributes', ['default_class' => 'form-control form-select'])
        @if ($allowMultipleValues)
            multiple
        @endif
        >

        @if ($entity_model::isColumnNullable($field['name']))
            <option value="">-</option>
        @endif

            @if (count($possible_values))
                @foreach ($possible_values as $key => $possible_value)
                    <option value="{{ $key }}"
                        @if (in_array($key, $field['value']))
                            selected
                        @endif
                    >{{ $possible_value }}</option>
                @endforeach
            @endif
    </select>

    {{-- HINT --}}
    @if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
@include('crud::fields.inc.wrapper_end')
