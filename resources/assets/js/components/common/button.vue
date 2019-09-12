<template>
	<button
		:is="elementComputed"
		class="e-button"
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
			elementComputed: function () {
				return this.href ? 'a' : 'button';
			},
		},
	};
</script>
