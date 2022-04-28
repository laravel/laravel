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
	<div class="inline-flex flex-col items-start relative cursor-pointer">
		<label-text :text="label" />

		<span class="grid md:grid-cols-2 gap-2">
			<label
				v-for="(option, index) in options"
				:key="index"
				class="flex items-center gap-x-2 relative cursor-pointer"
			>
				<span class="relative w-5 h-5 rounded-full border">
					<input
						v-model="value"
						v-bind="$attrs"
						:value="option.value"
						class="appearance-none absolute -inset-px rounded-full w-5 h-5"
						type="radio"
					>

					<span
						v-show="value === option.value"
						class="absolute inset-0.5 rounded-full bg-focus"
					/>
				</span>

				<span v-text="option.label" />
			</label>
		</span>

		<error-text :name="name" />
	</div>
</template>
