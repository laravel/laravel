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
	<label class="inline-flex flex-col items-start relative cursor-pointer">
		<div class="inline-flex items-start">
			<input
				v-model="value"
				:true-value="true"
				:false-value="false"
				type="checkbox"
				class="peer sr-only"
			>

			<div
				:class="[
					'relative flex-shrink-0 w-8 h-8 mr-2',
					'peer-focus:ring-2 peer-focus:ring-focus',
				]"
			>
				&nbsp;

				<div class="absolute top-1/2 left-0 w-full pt-full transform -translate-y-1/2">
					<div
						:class="[
							'flex items-center justify-center absolute inset-0',
							'bg-grey-100 border-1 border-grey-900',
							{
								'bg-grey-200': value,
							},
						]"
					>
						<e-icon
							:class="[
								'transform transition-transform duration-200 ease-out-cubic',
								value ? 'scale-90' : 'scale-0',
							]"
							size="w-full h-full"
							name="check"
						/>
					</div>
				</div>
			</div>

			<span
				class="self-center"
				aria-hidden="true"
				v-html="label"
			/>
		</div>

		<error-text
			v-if="errorMessage"
			:message="errorMessage"
		/>
	</label>
</template>
