@php
    $column['value'] = $column['value'] ?? data_get($entry, $column['name']);
    if(function_exists('enum_exists') && !empty($column['value']))  {
        if($column['value'] instanceof \UnitEnum) {
            $column['value'] = isset($column['enum_function']) ? $column['value']->{$column['enum_function']}() : ($column['value'] instanceof \BackedEnum ? $column['value']->value : $column['value']->name);
        }else{
            if(isset($column['enum_class'])) {
                $enumClassReflection = new \ReflectionEnum($column['enum_class']);
                if ($enumClassReflection->hasConstant($column['value'])) {
                    $enumClass = $enumClassReflection->getConstant($column['value']);
                }
                
                $column['value'] = isset($column['enum_function']) ? $enumClass->{$column['enum_function']}() : $column['value'];
            }elseif($column['value'] instanceof \Illuminate\Support\Collection) {
                $column['value'] = $column['value']->transform(function($item) use ($column) {
                    return $item instanceof \BackedEnum ? $item->value : $item->name;
                })->toArray();
            }
        }
    }

    if(!isset($column['options']) && is_array($column['value'])) {
        $column['options'] = (function() use ($entry, $column) {

            // if we are in a PHP version where PHP enums are not available, it can only be a database enum
            if(! function_exists('enum_exists')) {
                $options = $entity_model::getPossibleEnumValues($column['name']);
                return array_combine($options, $options);
            }
                    // developer can provide the enum class so that we extract the available options from it
            $enumClassReflection = isset($column['enum_class']) ? new \ReflectionEnum($column['enum_class']) : false;

            if(! $enumClassReflection) {
                // check for model casting
                $possibleEnumCast = $entry->getCasts()[$column['name']] ?? false;

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

            if(isset($column['enum_function']) && isset($options)) {
                $options = array_map(function($item) use ($column, $enumClassReflection) {
                    if ($enumClassReflection->hasConstant($item)) {
                        return $enumClassReflection->getConstant($item)->{$column['enum_function']}();
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
            $options = $entity_model::getPossibleEnumValues($column['name']);
            return array_combine($options, $options);
        })();
    }
@endphp

@include(isset($column['options']) ? 'crud::columns.select_from_array' : 'crud::columns.text')