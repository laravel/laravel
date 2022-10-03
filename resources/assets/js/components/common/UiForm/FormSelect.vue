<script setup>
	import { Field as RenderlessSelect } from 'vee-validate';

	import LabelText from './LabelText';
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
	<label class="inline-flex flex-col gap-y-2 cursor-pointer">
		<label-text :text="label" />

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
						'pr-12 border appearance-none w-full',
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

			<ui-icon
				name="chevron-right"
				:class="[
					'absolute top-1/2 right-0',
					'mr-em',
					'transform rotate-90 -translate-y-1/2',
					'pointer-events-none',
				]"
			/>
		</div>

		<error-text :name="name" />
	</label>
</template>
