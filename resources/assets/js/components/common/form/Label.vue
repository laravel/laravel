<template>
	<component
		:is="$props.element"
		class="block mb-1/2em cursor-pointer font-bold leading-snug"
		v-html="$props.text"
	/>
</template>

<script>
	export default {
		props: {
			element: {
				type: String,
				default: 'label',
			},

			text: {
				type: String,
				required: true,
			},
		},
	};
</script>
