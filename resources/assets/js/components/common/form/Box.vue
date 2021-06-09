<template>
	<label
		class="inline-flex items-start relative cursor-pointer"
		:disabled="$props.context.attributes.disabled"
		:for="$props.context.id"
	>
		<input
			v-model="$props.context.model"
			v-bind="$props.context.attributes"
			:value="$props.context.value"
			:type="$props.context.type"
			class="sr-only"
		>

		<div class="relative flex-shrink-0 w-8 h-8 mr-2">
			&nbsp;

			<div class="absolute top-1/2 left-0 w-full pt-full transform -translate-y-1/2">
				<div
					:class="[
						'flex items-center justify-center absolute inset-0',
						'bg-grey-100 border-1 border-grey-900',
						{
							'rounded-full': $props.context.type === 'radio',
							'bg-grey-200': $props.context.hasValue,
						},
					]"
				>
					<icon
						:class="[
							'transform transition-transform duration-200 ease-out-cubic',
							$props.context.hasValue ? 'scale-90' : 'scale-0',
						]"
						name="check"
					/>
				</div>
			</div>
		</div>

		<span
			class="self-center"
			aria-hidden="true"
			v-html="$props.context.label"
		/>

		<span class="e-focus-sibling absolute top-0 left-0 z-1 w-8 h-8 pointer-events-none" />
	</label>
</template>

<script>
	export default {
		props: {
			context: {
				type: Object,
				required: true,
			},
		},
	};
</script>
