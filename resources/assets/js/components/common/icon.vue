<template>
	<svg
		class="icon"
		:class="`icon--${name}`"
	>
		<use :xlink:href="`/compiled/img/sprite.svg#${name}`" />
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
