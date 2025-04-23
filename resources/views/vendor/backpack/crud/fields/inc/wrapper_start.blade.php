@php
	$field['wrapper'] = $field['wrapper'] ?? $field['wrapperAttributes'] ?? [];

    // each wrapper attribute can be a callback or a string
    // for those that are callbacks, run the callbacks to get the final string to use
    foreach($field['wrapper'] as $attributeKey => $value) {
        $field['wrapper'][$attributeKey] = !is_string($value) && $value instanceof \Closure ? $value($crud, $field, $entry ?? null) : $value ?? '';
    }
	// if the field is required in any of the crud validators (FormRequest, controller validation or field validation) 
	// we add an astherisc for it. Case it's a subfield, that check is done upstream in repeatable_row. 
	// the reason for that is that here the field name is already the repeatable name: parent[row][fieldName]
	if(!isset($field['parentFieldName']) || !$field['parentFieldName']) {
		$fieldName = $crud->holdsMultipleInputs($field['name']) ? explode(',', $field['name']) : [$field['name']];
		foreach($fieldName as $inputName) {
			$required = (isset($action) && $crud->isRequired($inputName)) ? ' required' : '';
			// if any of the hold inputs is required, the whole field is required
			if(!empty($required)) {
				break;
			}
		}
	}
	
	// if the developer has intentionally set the required attribute on the field
	// forget whatever is in the FormRequest, do what the developer wants
	// subfields also get here with `showAsterisk` already set.
	$required = isset($field['showAsterisk']) ? ($field['showAsterisk'] ? ' required' : '') : ($required ?? '');
	
	$field['wrapper']['class'] = $field['wrapper']['class'] ?? "form-group col-sm-12 mb-3";
	$field['wrapper']['class'] = $field['wrapper']['class'].$required;
	$field['wrapper']['element'] = $field['wrapper']['element'] ?? 'div';
	$field['wrapper']['bp-field-wrapper'] = 'true';
	$field['wrapper']['bp-field-name'] = square_brackets_to_dots(implode(',', (array)$field['name']));
	$field['wrapper']['bp-field-type'] = $field['type'];
	$field['wrapper']['bp-section'] = 'crud-field';
@endphp

<{{ $field['wrapper']['element'] }}
	@foreach($field['wrapper'] as $attribute => $value)
	    {{ $attribute }}="{{ $value }}"
	@endforeach
>
