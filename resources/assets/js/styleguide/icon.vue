<template>
	<svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
		<path v-for="(path, index) in cPaths" :key="index" :d="path" />
	</svg>
</template>

<script>
	const icons = {
		code: [
			'M16 18l6-6-6-6',
			'M8 6l-6 6 6 6',
		],
		copy: [
			'M17 10H3',
			'M21 6H3',
			'M21 14H3',
			'M17 18H3',
		],
		external: [
			'M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6',
			'M15 3h6v6',
			'M10 14L21 3',
		],
		play: [
			'M5 3l14 9-14 9V3z',
		],
	};

	export default {
		props: {
			name: {
				type: String,
				required: true,
			},
		},

		computed: {
			cPaths() {
				return icons[this.$props.name];
			},
		},
	};
</script>
