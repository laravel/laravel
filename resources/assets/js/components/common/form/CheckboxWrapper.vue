<template>
	<div>
		<label
			class="flex items-start"
			:disabled="$attrs.disabled"
		>
			<slot
				v-if="$slots.default"
			/>

			<input
				v-else
				:type="$props.type"
				class="e-checkbox sr-only"
				:checked="$data.checked"
				v-bind="$attrs"
				@change="updateInput"
				@focus="$data.hasKeyboardFocus = isKeyboard()"
				@blur="$data.hasKeyboardFocus = false"
			>

			<div
				:class="[
					'flex-shrink-0 w-5 mr-2 py-1px',
					{ 'shadow-focus': $data.hasKeyboardFocus },
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
							v-if="$data.checked"
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

	import Input from '../../../mixins/input';

	export default {
		components: {
			ErrorText,
		},

		mixins: [Input],

		inheritAttrs: false,

		model: {
			prop: 'value',
			event: 'input',
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

			trueValue: {
				type: [String, Boolean],
				default: true,
			},

			errors: {
				type: Array,
				default: null,
			},
		},

		data() {
			return {
				checked: null,
				hasKeyboardFocus: false,
			};
		},

		methods: {
			updateInput(event) {
				const isChecked = event.target.checked;

				this.$data.checked = isChecked;

				this.$emit('input', isChecked ? this.trueValue : false);
			},
		},
	};
</script>
