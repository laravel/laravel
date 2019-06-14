<template>
	<div
		v-if="text"
		class="icon-text"
	>
		<icon v-if="!iconAfter" :name="name" />

		<span v-html="text" />

		<icon v-if="iconAfter" :name="name" />
	</div>

	<svg
		v-else
		class="icon"
		:class="[
			`icon--${name}`,
			{
				'icon--inline': inline
			}
		]"
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

			iconAfter: Boolean,

			text: {
				type: String,
				default: null,
			},

			inline: Boolean,
		},
	};
</script>
