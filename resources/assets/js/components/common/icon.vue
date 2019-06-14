<template>
	<svg
		class="icon"
		:class="`icon--${name}`"
	>
		<use :xlink:href="`/assets/img/sprite.svg#${name}`" />
	</svg>
</template>

<script>
	export default {
		props: {
			name: {
				type: String,
				required: true,
			},
		},
	};
</script>
