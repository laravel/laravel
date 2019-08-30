<template>
	<div>
		<e-label
			v-if="label && !isCheckboxOrRadio"
			:for="id"
			:text="label"
		/>

		<input
			:is="element"
			:id="id"
			:type="type"
			:value="value"
			:class="[
				type,
				{
					input: !isCheckboxOrRadio,
				},
			]"
			:placeholder="placeholder"
			:rows="rows"
			@input="$emit('input', $event.target.value)"
		/>

		<label
			v-if="label && isCheckboxOrRadio"
			:for="id"
			class="checkbox-label"
		>
			<span class="checkbox-label__icon-wrapper">
				<icon name="check" class="checkbox-label__icon" />
			</span>

			<span
				class="checkbox-label__content"
				v-html="label"
			/>
		</label>

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

			placeholder: {
				type: String,
				default: null,
			},

			rows: {
				type: Number,
				default: null,
			},

			error: {
				type: Object,
				default: () => {},
			},
		},

		computed: {
			element() {
				const types = ['textarea'];

				return types.includes(this.$props.type) ? this.$props.type : 'input';
			},

			isCheckboxOrRadio() {
				const inputs = ['checkbox', 'radio'];

				return inputs.includes(this.$props.type);
			},
		},
	};
</script>
