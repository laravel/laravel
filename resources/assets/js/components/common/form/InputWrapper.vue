<template>
	<component
		:is="cElement"
		class="block"
	>
		<e-label
			v-if="$props.label"
			element="span"
			:text="$props.label"
		/>

		<slot
			v-if="$slots.default"
		/>

		<component
			:is="$props.element"
			v-else
			:type="cInputType"
			:class="cInputClass"
			:options="$props.options"
			v-bind="$attrs"
			@input="$emit('input', $event.target.value)"
		/>

		<error-text :errors="$props.errors" />
	</component>
</template>

<script>
	import ELabel from './Label';
	import ESelect from './Select';
	import ErrorText from '../ErrorText';

	export default {
		components: {
			ELabel,
			ESelect,
			ErrorText,
		},

		inheritAttrs: false,

		model: {
			prop: 'value',
			event: 'input',
		},

		props: {
			element: {
				type: String,
				default: 'input',
			},

			label: {
				type: String,
				default: null,
			},

			errors: {
				type: Array,
				default: null,
			},

			options: {
				type: [Array, Object],
				default: null,
			},
		},

		computed: {
			cElement() {
				return this.$props.label ? 'label' : 'div';
			},

			cInputType() {
				return this.$attrs.type || (this.$props.element === 'input' ? 'text' : null);
			},

			cInputClass() {
				const classes = {
					'e-select': 'e-select',
				};

				return [
					classes[this.$props.element] || 'e-input',
					{
						'font-system': this.$attrs.type === 'password',
					},
				];
			},
		},
	};
</script>
