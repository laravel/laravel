<script setup>
	import { useField } from 'vee-validate';

	import ErrorText from './ErrorText';

	const props = defineProps({
		label: {
			type: String,
			required: true,
		},

		name: {
			type: String,
			required: true,
		},

		type: {
			type: String,
			default: 'text',
		},

		placeholder: {
			type: String,
			default: null,
		},

		rules: {
			type: String,
			default: null,
		},

		validationName: {
			type: String,
			default: null,
		},
	});

	const {
		value,
		errorMessage,
		handleChange,
		handleBlur,
	} = useField(props.name, props.rules, {
		label: props.validationName || props.label,
	});
</script>

<template>
	<label class="flex flex-col gap-y-1">
		<span v-text="label" />

		<input
			v-model="value"
			v-bind="$attrs"
			:type="type"
			:class="[
				'border',
				{
					'border-red': errorMessage,
				},
			]"
			:name="name"
			:placeholder="placeholder || label"
			@change="handleChange"
			@input="handleChange"
			@blur="handleBlur"
		>

		<error-text
			v-if="errorMessage"
			:message="errorMessage"
		/>
	</label>
</template>
