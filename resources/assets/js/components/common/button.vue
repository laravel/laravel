<template>
	<button
		class="button"
		:class="classList"
		v-text="$props.text"
	/>
</template>

<script>
	export default {
		props: {
			large: Boolean,

			negative: Boolean,

			positive: Boolean,

			small: Boolean,

			text: {
				type: String,
				required: true,
			},
		},

		computed: {
			classList() {
				return {
					'button--large': this.$props.large,
					'button--negative': this.$props.negative,
					'button--positive': this.$props.positive,
					'button--small': this.$props.small,
				};
			},
		},
	};

</script>
