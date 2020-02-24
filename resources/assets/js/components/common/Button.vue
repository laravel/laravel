<template>
	<component
		:is="cElement"
		class="e-button"
		:href="$props.href"
		v-html="$props.text"
	/>
</template>

<script>
	export default {
		props: {
			text: {
				type: String,
				required: true,
			},

			href: {
				type: String,
				default: null,
			},
		},

		computed: {
			cElement: function () {
				return this.href ? 'a' : 'button';
			},
		},
	};
</script>
