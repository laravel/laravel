<template>
	<div>
		<e-label
			v-if="label && !isCheckboxOrRadio"
			:for="id"
			:text="label"
		/>

		<input
			:id="id"
			:type="type"
			:value="value"
			:class="type"
			@input="$emit('input', $event.target.value)"
		>

		<label
			v-if="label && isCheckboxOrRadio"
			:for="id"
			v-html="label"
		/>

		<error-text :errors="error" />
	</div>
</template>

<script>
	import ErrorText from './error-text.vue';

	export default {
		components: {
			'error-text': ErrorText,
		},

		props: {
			value: {
				type: String,
				default: null,
			},

			id: {
				type: String,
				required: true,
			},

			type: {
				type: String,
				default: 'text',
			},

			label: {
				type: String,
				default: null,
			},

			error: {
				type: Object,
				default: () => {},
			},
		},

		computed: {
			isCheckboxOrRadio() {
				const inputs = ['checkbox', 'radio'];

				return inputs.includes(this.$props.type);
			},
		},
	};
</script>
