<template>
	<div class="flex items-center">
		<icon v-if="!iconAfter" :name="icon" />

		<span class="ml-1" v-html="text" />

		<icon v-if="iconAfter" :name="icon" class="ml-1" />
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
