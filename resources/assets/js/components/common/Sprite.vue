<template>
	<svg>
		<use :xlink:href="`${$props.url}#${$props.name}`" />
	</svg>
</template>

<script>
	export default {
		props: {
			name: {
				type: String,
				required: true,
			},

			url: {
				type: String,
				default: window.app.sprite,
			},
		},
	};
</script>
