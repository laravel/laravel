<template>
	<div class="breadcrumb">
		<template v-for="(page, index) in pages">
			<span
				v-if="index === ($props.pages.length - 1)"
				:key="`label-${index}`"
				class="breadcrumb__item"
				v-text="page.title"
			/>

			<template v-else>
				<a
					:key="`link-${index}`"
					class="breadcrumb__item breadcrumb__item--link"
					:href="page.url"
					v-text="page.title"
				/>

				<icon
					:key="`icon-${index}`"
					class="breadcrumb__icon"
					name="chevron-right"
				/>
			</template>
		</template>
	</div>
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
