<template>
	<div class="block relative">
		<select
			v-model="$props.context.model"
			v-bind="$props.context.attributes"
			class="pr-12 appearance-none"
		>
			<option
				value=""
				disabled
				v-html="$props.context.placeholder"
			/>

			<option
				v-for="(option, index) in $props.context.options"
				:key="index"
				:value="option.value"
				v-html="option.label"
			/>
		</select>

		<icon
			name="chevron-right"
			:class="[
				'absolute top-1/2 right-0',
				'w-em h-em -mt-1/2em mr-em',
				'transform rotate-90',
				'pointer-events-none',
			]"
		/>
	</div>
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
