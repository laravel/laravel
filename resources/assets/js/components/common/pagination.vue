<template>
	<ol class="pagination">
		<li
			v-for="(page, index) in pages"
			:key="index"
			class="pagination__item"
			:class="[
				page.modifier ? `pagination__item--${page.modifier}` : null,
				{
					'pagination__item--disabled': page.disabled,
					'pagination__item--current': page.current
				}
			]"
		>
			<a
				:is="page.url ? 'a' : 'span'"
				:href="page.url"
				:class="[
					page.gap ? 'pagination__gap' : 'pagination__link'
				]"
				v-html="page.title"
			/>
		</li>
	</ol>
</template>

<script>
	export default {
		props: {
			pages: {
				type: Array,
				required: true,
			},
		},
	};
</script>
