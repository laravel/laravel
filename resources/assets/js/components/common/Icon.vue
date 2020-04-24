<template>
	<sprite
		class="inline-block w-em h-em fill-current align-top"
		:name="$props.name"
	/>
</template>

<script>
	import Sprite from './Sprite';

	export default {
		components: {
			Sprite,
		},

		props: {
			name: {
				type: String,
				required: true,
			},
		},
	};
</script>
