{{-- select_grouped --}}
@php
    $current_value = old_empty_or_null($field['name'], '') ??  $field['value'] ?? $field['default'] ?? '';
    $field['allows_null'] = $field['allows_null'] ?? $field['model']::isColumnNullable($field['name']);
@endphp

@include('crud::fields.inc.wrapper_start')
    <label>{!! $field['label'] !!}</label>
    @include('crud::fields.inc.translatable_icon')
    @php
        $related_model = $field['model'];
        $group_by_model = (new $related_model)->{$field['group_by']}()->getRelated();
        $categories = $group_by_model::with($field['group_by_relationship_back'])->get();

        if (isset($field['model'])) {
            $categorylessEntries = $related_model::doesnthave($field['group_by'])->get();
        }
    @endphp

    @if(isset($field['prefix']) || isset($field['suffix'])) <div class="input-group"> @endif
        @if(isset($field['prefix'])) <div class="input-group-prepend"><span class="input-group-text">{!! $field['prefix'] !!}</span></div> @endif
        <select
            name="{{ $field['name'] }}"
            @include('crud::fields.inc.attributes', ['default_class' => 'form-control form-select'])
            >

                @if ($field['allows_null'])
                    <option value="">-</option>
                @endif

                @if (isset($field['model']) && isset($field['group_by']))
                    @foreach ($categories as $category)
                        <optgroup label="{{ $category->{$field['group_by_attribute']} }}">
                            @foreach ($category->{$field['group_by_relationship_back']} as $subEntry)
                                <option value="{{ $subEntry->getKey() }}"
                                    @if ( ( old($field['name']) && old($field['name']) == $subEntry->getKey() ) || (isset($field['value']) && $subEntry->getKey()==$field['value']))
                                        selected
                                    @endif
                                >{{ $subEntry->{$field['attribute']} }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach

                    @if ($categorylessEntries->count())
                        <optgroup label="-">
                            @foreach ($categorylessEntries as $subEntry)

                                @if($current_value == $subEntry->getKey())
                                    <option value="{{ $subEntry->getKey() }}" selected>{{ $subEntry->{$field['attribute']} }}</option>
                                @else
                                    <option value="{{ $subEntry->getKey() }}">{{ $subEntry->{$field['attribute']} }}</option>
                                @endif
                            @endforeach
                        </optgroup>
                    @endif
                @endif
        </select>
        @if(isset($field['suffix'])) <span class="input-group-text">{!! $field['suffix'] !!}</span> @endif
    @if(isset($field['prefix']) || isset($field['suffix'])) </div> @endif

    {{-- HINT --}}
    @if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
@include('crud::fields.inc.wrapper_end')
