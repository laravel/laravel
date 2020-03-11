<template>
	<div class="block relative">
		<select
			:value="$props.value"
			class="e-input pr-12 appearance-none"
			v-bind="$attrs"
			v-on="$listeners"
			@change="$emit('update', $event.target.value)"
		>
			<option
				:value="null"
				disabled
				v-html="$options.filters.trans('global.form.select_default')"
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

<style lang="scss">
	select::-ms-expand {
		display: none;
	}
</style>

<script>
	export default {
		inheritAttrs: false,

		props: {
			value: {
				type: String,
				default: null,
			},

			options: {
				type: [Array, Object],
				default: () => {},
			},
		},
	};
</script>
