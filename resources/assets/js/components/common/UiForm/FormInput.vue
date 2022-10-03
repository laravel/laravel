<script setup>
	import { useField } from 'vee-validate';

	import LabelText from './LabelText';
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

		placeholder: {
			type: String,
			default: null,
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
	<label class="flex flex-col gap-y-2 cursor-pointer">
		<label-text :text="label" />

		<input
			v-model="value"
			v-bind="$attrs"
			:type="type"
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
		>

		<error-text :name="name" />
	</label>
</template>
