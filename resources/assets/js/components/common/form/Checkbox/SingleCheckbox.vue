<script setup>
	import { useField } from 'vee-validate';
	import VisuallyChecked from './VisuallyChecked';
	import ErrorText from '../ErrorText';

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
	<label class="inline-flex flex-col items-start relative cursor-pointer">
		<div class="inline-flex items-start">
			<input
				v-model="value"
				v-bind="$attrs"
				:true-value="true"
				:false-value="false"
				type="checkbox"
				class="peer sr-only"
			>

			<visually-checked :checked="value" />

			<span
				class="self-center"
				aria-hidden="true"
				v-html="label"
			/>
		</div>

		<error-text :name="name" />
	</label>
</template>
