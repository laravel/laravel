<script setup>
	import { useField } from 'vee-validate';

	import ErrorText from './ErrorText';

	const props = defineProps({
		cols: {
			type: Number,
			default: 50,
		},

		label: {
			type: String,
			required: true,
		},

		name: {
			type: String,
			required: true,
		},

		placeholder: {
			type: String,
			default: null,
		},

		rows: {
			type: Number,
			default: 4,
		},

		rules: {
			type: String,
			default: null,
		},

		type: {
			type: String,
			default: 'text',
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

		<textarea
			v-model="value"
			v-bind="$attrs"
			:rows="rows"
			:cols="cols"
			:class="[
				'border placeholder-grey-400',
				{
					'border-red': errorMessage,
				},
			]"
			:name="name"
			:placeholder="placeholder"
			@change="handleChange"
			@input="handleChange"
			@blur="handleBlur"
		/>

		<error-text :name="name" />
	</label>
</template>
