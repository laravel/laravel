<template>
	<component
		:is="cTag"
		:class="$props.ratioClass"
		class="block relative w-full"
		v-bind="cAttrs"
	>
		<slot>
			<e-img overlay v-bind="$attrs" />
		</slot>
	</component>
</template>

<script>
	import EImg from './Img';

	export default {
		components: {
			EImg,
		},

		inheritAttrs: false,

		props: {
			ratioClass: {
				type: String,
				default: 'pt-9/16',
			},

			tag: {
				type: String,
				default: 'div',
			},
		},

		computed: {
			cAttrs() {
				return this.$slots.default ? this.$attrs : null;
			},

			cTag() {
				return this.$attrs.href ? 'a' : this.$props.tag;
			},
		},
	};
</script>
