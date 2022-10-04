<script setup>
	import { useField } from 'vee-validate';

	import ErrorText from './ErrorText';
	import LabelText from './LabelText';
	import VisuallyChecked from './FormCheckbox/VisuallyChecked';

	const props = defineProps({
		label: {
			type: String,
			required: true,
		},

		name: {
			type: String,
			required: true,
		},

		options: {
			type: Array,
			required: true,
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

	const { value } = useField(props.name, props.rules, {
		label: props.validationName || props.label,
	});
</script>

<template>
	<div class="flex flex-col gap-y-2">
		<label-text :text="label" />

		<span class="flex flex-col gap-y-2">
			<label
				v-for="(option, index) in options"
				:key="index"
				class="flex items-start gap-x-2 cursor-pointer"
			>
				<input
					v-model="value"
					type="radio"
					v-bind="$attrs"
					:value="option.value"
					class="peer sr-only"
				>

				<visually-checked :checked="value === option.value" radio />

				<span v-text="option.label" />
			</label>
		</span>

		<error-text :name="name" />
	</div>
</template>
