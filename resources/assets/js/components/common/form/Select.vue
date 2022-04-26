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

		options: {
			type: Array,
			required: true,
		},

		placeholder: {
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

	const {
		value,
		errorMessage,
	} = useField(props.name, props.rules, {
		label: props.validationName || props.label,
	});
</script>

<template>
	<div class="inline-flex flex-col">
		<label
			:for="name"
			v-text="label"
		/>

		<div class="flex relative">
			<select
				:id="name"
				v-model="value"
				v-bind="$attrs"
				:class="[
					'pr-12 border appearance-none',
					{ 'border-red': errorMessage },
				]"
			>
				<option
					value=""
					disabled
					hidden
					v-html="placeholder"
				/>

				<option
					v-for="(option, index) in options"
					:key="index"
					:value="option.value"
					v-html="option.label"
				/>
			</select>

			<span
				v-if="placeholder && !value"
				:class="[
					'absolute inset-0 right-12',
					'truncate text-grey-400 pointer-events-none',
				]"
				v-text="placeholder"
			/>

			<e-icon
				name="chevron-right"
				:class="[
					'absolute top-1/2 right-0',
					'-mt-1/2em mr-em',
					'transform rotate-90',
					'pointer-events-none',
				]"
			/>
		</div>

		<error-text
			v-if="errorMessage"
			:message="errorMessage"
		/>
	</div>
</template>
