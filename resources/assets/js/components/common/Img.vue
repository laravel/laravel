<template>
	<picture :key="cSrc">
		<source
			v-for="item in cSrcsetWebP"
			:key="item.srcset"
			v-bind="item"
		>

		<source
			v-for="item in cSrcset"
			:key="item.srcset"
			v-bind="item"
		>

		<img
			v-bind="$attrs"
			:class="cClassList"
			:loading="$props.loading"
			:src="cSrc"
		>
	</picture>
</template>

<script>
	export default {
		inheritAttrs: false,

		props: {
			classList: {
				type: [Array, String],
				default: null,
			},

			loading: {
				type: String,
				default: 'lazy',
			},

			overlay: Boolean,

			sizes: {
				type: Array,
				default: null,
			},

			src: {
				type: Array,
				required: true,
			},

			webp: {
				type: Array,
				required: true,
			},
		},

		computed: {
			cClassList() {
				return [
					[this.$props.classList].flat(),
					{ 'absolute top-0 left-0 w-full h-full object-cover': this.$props.overlay },
				];
			},

			cSrc() {
				return this.$props.src.slice(-1)[0];
			},

			cSrcset() {
				return this.srcSet();
			},

			cSrcsetWebP() {
				return this.srcSet(true);
			},
		},

		methods: {
			srcSet(webp) {
				const type = webp ? 'image/webp' : null;
				const sizes = this.$props.sizes || [];

				return this.$props[webp ? 'webp' : 'src']
					.slice(0, webp ? undefined : -1)
					.map((srcset, index) => ({
						srcset,
						media: sizes[index] ? `(min-width: ${sizes[index]}px)` : null,
						type,
					}));
			},
		},
	};
</script>
