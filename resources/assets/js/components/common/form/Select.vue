<template>
	<div class="block relative">
		<select
			:value="$props.value"
			class="e-input pr-12 appearance-none"
			v-bind="$attrs"
			v-on="$listeners"
			@change="$emit('input', $event)"
		>
			<option
				:value="null"
				disabled
				v-html="cDefaultOption"
			/>

			<option
				v-for="(option, key) in $props.options"
				:key="key"
				:value="Array.isArray($props.options) ? option : key"
				v-html="option"
			/>
		</select>

		<icon
			name="chevron-down"
			:class="[
				'absolute top-1/2 right-0',
				'w-em h-em -mt-1/2em mr-em',
				'pointer-events-none',
			]"
		/>
	</div>
</template>

<script>
	export default {
		inheritAttrs: false,

		props: {
			default: {
				type: String,
				default: null,
			},

			value: {
				type: String,
				default: null,
			},

			options: {
				type: [Array, Object],
				default: () => {},
			},
		},

		computed: {
			cDefaultOption() {
				return this.$props.default || this.$options.filters.trans('global.form.select_default');
			},
		},
	};
</script>
