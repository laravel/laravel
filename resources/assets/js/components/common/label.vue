<template>
	<label
		class="block mb-half-em pointer-cursor font-bold leading-small"
		v-html="text"
	/>
</template>

<script>
	export default {
		props: {
			text: {
				type: String,
				required: true,
			},
		},
	};
</script>
