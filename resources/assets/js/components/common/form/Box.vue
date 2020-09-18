<template>
	<label
		class="flex items-start"
		:disabled="$props.context.attributes.disabled"
		:for="$props.context.id"
	>
		<input
			v-model="$props.context.model"
			v-bind="$props.context.attributes"
			:value="$props.context.value"
			:type="$props.context.type"
			class="sr-only"
			@focus="$data.hasKeyboardFocus = isKeyboard()"
			@blur="$data.hasKeyboardFocus = false"
		>

		<div
			:class="[
				'flex-shrink-0 w-5 mr-2 py-1px',
				{
					'shadow-focus': $data.hasKeyboardFocus,
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
						v-if="$props.context.hasValue"
						d="M 11.5 20 L 17.5 27 L 28.5 16"
					/>

					<circle
						v-if="$props.context.type === 'radio'"
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
			aria-hidden="true"
			v-html="$props.context.label"
		/>
	</label>
</template>

<script>
	import Input from '../../../mixins/input';

	export default {
		mixins: [Input],

		props: {
			context: {
				type: Object,
				required: true,
			},
		},
	};
</script>
