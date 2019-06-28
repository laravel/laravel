<template>
	<div class="icon-text">
		<icon v-if="!iconAfter" :name="icon" />

		<span v-html="text" />

		<icon v-if="iconAfter" :name="icon" />
	</div>
</template>

<script>
	export default {
		props: {
			text: {
				type: String,
				required: true,
			},

			icon: {
				type: String,
				required: true,
			},

			iconAfter: Boolean,

			inline: Boolean,
		},
	};
</script>
