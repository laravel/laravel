<template>
	<div>
		<label
			:class="[
				'flex items-start',
				{
					'e-is-invalid': $props.errors,
				},
			]"
			:disabled="$attrs.disabled"
		>
			<slot
				v-if="$slots.default"
			/>

			<input
				v-else
				:type="$props.type"
				class="e-checkbox sr-only"
				:checked="cChecked"
				v-bind="$attrs"
				@change="updateInput"
				@focus="onWhatFocus"
				@blur="onWhatBlur"
			>

			<div
				:class="[
					'flex-shrink-0 w-5 mr-2 py-1px',
					{
						'shadow-focus': hasKeyboardFocus,
					},
				]"
			>
				<placeholder class="my-1px pt-full">
					<svg
						class="w-full"
						fill="none"
						viewBox="0 0 40 40"
						stroke="currentColor"
						xmlns="http://www.w3.org/2000/svg"
						stroke-width="4"
						stroke-linecap="round"
						stroke-linejoin="round"
					>
						<path
							v-if="cChecked"
							d="M 11.5 20 L 17.5 27 L 28.5 16"
						/>

						<circle
							v-if="$props.type === 'radio'"
							cx="20"
							cy="20"
							r="18"
						/>

						<rect
							v-else
							x="2"
							y="2"
							width="36"
							height="36"
						/>
					</svg>
				</placeholder>
			</div>

			<span
				class="leading-normal self-center"
				v-html="$props.label"
			/>
		</label>

		<error-text :errors="$props.errors" />
	</div>
</template>

<script>
	import ErrorText from '../ErrorText';

	import WhatFocus from '../../../mixins/what-focus';

	export default {
		components: {
			ErrorText,
		},

		mixins: [
			WhatFocus,
		],

		inheritAttrs: false,

		model: {
			prop: 'modelValue',
			event: 'change',
		},

		props: {
			label: {
				type: String,
				required: true,
			},

			type: {
				type: String,
				default: 'checkbox',
			},

			value: {
				type: String,
				default: null,
			},

			modelValue: {
				type: [String, Boolean],
				default: null,
			},

			trueValue: {
				type: [String, Boolean],
				default: true,
			},

			falseValue: {
				type: [String, Boolean],
				default: false,
			},

			errors: {
				type: Array,
				default: null,
			},
		},

		computed: {
			cChecked() {
				if (this.modelValue instanceof Array) {
					return this.modelValue.includes(this.value);
				}

				return this.modelValue === this.trueValue;
			},
		},

		methods: {
			updateInput(event) {
				const isChecked = event.target.checked;

				if (this.modelValue instanceof Array) {
					const newValue = [...this.modelValue];

					if (isChecked) {
						newValue.push(this.value);
					} else {
						newValue.splice(newValue.indexOf(this.value), 1);
					}

					this.$emit('change', newValue);
				} else {
					this.$emit('change', isChecked ? this.trueValue : this.falseValue);
				}
			},
		},
	};
</script>
