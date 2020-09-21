<template>
	<component
		:is="cElement"
		v-bind="$props.context.attributes"
		:class="[
			'relative',
			'p-4',
			'bg-brand-red text-white',
			'font-body text-base font-normal leading-none',
			'no-underline',
		]"
		:href="$props.href"
		v-html="$props.context.label || $props.text"
	/>
</template>

<script>
	export default {
		props: {
			context: {
				type: Object,
				default: () => ({}),
			},

			text: {
				type: String,
				default: null,
			},
		},

		computed: {
			cElement: function () {
				return this.$attrs.href ? 'a' : 'button';
			},
		},
	};
</script>
