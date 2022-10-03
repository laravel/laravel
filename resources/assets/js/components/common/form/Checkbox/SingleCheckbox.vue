<script setup>
	import { useField } from 'vee-validate';

	import ErrorText from '../ErrorText';
	import VisuallyChecked from './VisuallyChecked';

	const props = defineProps({
		label: {
			type: String,
			required: true,
		},

		name: {
			type: String,
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
		<div class="inline-flex items-start gap-x-2">
			<input
				v-model="value"
				v-bind="$attrs"
				:true-value="true"
				:false-value="false"
				type="checkbox"
				class="peer sr-only"
			>

			<visually-checked :checked="value" />

			<span v-text="label" />
		</div>

		<error-text :name="name" />
	</label>
</template>
