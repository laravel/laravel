<template>
	<svg class="inline-block w-em h-em fill-current align-top">
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
