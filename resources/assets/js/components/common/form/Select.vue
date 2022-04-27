<script setup>
	import { Field as RenderlessSelect } from 'vee-validate';
	import ErrorText from './ErrorText';

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
</script>

<template>
	<div class="inline-flex flex-col">
		<label
			:for="name"
			v-text="label"
		/>

		<div class="flex relative">
			<renderless-select
				:id="name"
				v-slot="{ field, errorMessage }"
				v-bind="$attrs"
				:name="name"
				:label="validationName || label"
				:rules="rules"
			>
				<select
					:class="[
						'pr-12 border appearance-none',
						{ 'border-red': errorMessage },
					]"
					v-bind="field"
				>
					<option
						v-if="placeholder"
						value=""
						selected
						disabled
						v-html="placeholder"
					/>

					<option
						v-for="(option, index) in options"
						:key="index"
						:value="option.value"
						v-html="option.label"
					/>
				</select>
			</renderless-select>

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

		<error-text :name="name" />
	</div>
</template>
