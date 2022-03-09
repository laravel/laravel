<script setup>
	const props = defineProps({
		classList: {
			type: [Array, String],
			default: null,
		},

		loading: {
			type: String,
			default: 'lazy',
		},

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

		overlay: Boolean,
	});

	const srcSet = (webp) => {
		const type = webp ? 'image/webp' : null;
		const sizes = props.sizes || [];

		return props[webp ? 'webp' : 'src']
			.slice(0, webp ? undefined : -1)
			.map((srcset, index) => ({
				srcset,
				media: sizes[index] ? `(min-width: ${sizes[index]}px)` : null,
				type,
			}));
	};

	const cClassList = [
		[props.classList].flat(),
		{ 'absolute top-0 left-0 w-full h-full object-cover': props.overlay },
	];
	const cSrc = props.src.slice(-1)[0];
	const cSrcset = srcSet();
	const cSrcsetWebP = srcSet(true);
</script>

<script>
	export default {
		name: 'EImg',

		inheritAttrs: false,
	};
</script>

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
