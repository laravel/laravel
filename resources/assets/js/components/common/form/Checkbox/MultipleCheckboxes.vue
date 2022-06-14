<script setup>
	import { Field as RenderlessCheckbox } from 'vee-validate';
	import VisuallyChecked from './VisuallyChecked';
	import LabelText from '../LabelText';
	import ErrorText from '../ErrorText';

	defineProps({
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
</script>

<template>
	<div class="flex flex-col gap-y-2">
		<label-text :text="label" />

		<div class="flex flex-col gap-y-2">
			<renderless-checkbox
				v-for="(option, index) in options"
				:key="index"
				v-slot="{ field }"
				type="checkbox"
				:name="name"
				:label="validationName || label"
				:value="option.value"
				:rules="rules"
			>
				<label class="flex items-center gap-x-1">
					<input
						:name="name"
						type="checkbox"
						v-bind="field"
						class="peer sr-only"
						:value="option.value"
					>

					<visually-checked :checked="field.checked" />

					<span v-text="option.label" />
				</label>
			</renderless-checkbox>
		</div>

		<error-text :name="name" />
	</div>
</template>
