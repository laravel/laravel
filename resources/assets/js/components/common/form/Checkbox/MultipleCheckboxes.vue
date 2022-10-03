<script setup>
	import { Field as RenderlessCheckbox } from 'vee-validate';

	import ErrorText from '../ErrorText';
	import LabelText from '../LabelText';
	import VisuallyChecked from './VisuallyChecked';

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
				<label class="flex items-start gap-x-2">
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
