<template>
	<form
		:disabled="$data.isSubmitting"
		novalidate
		@submit.prevent="onSubmit"
	>
		<component
			:is="field.component || 'input-wrapper'"
			v-for="(field, index) in $props.fields"
			:key="`${field.name}-${index}`"
			ref="fields"
			v-model="$data.form[field.name]"
			v-bind="field"
			:errors="$data.errors[field.name]"
			:class="{
				'mt-8': index,
			}"
		/>
	</form>
</template>

<script>
	import Form from '../../mixins/form';

	export default {
		mixins: [Form],

		props: {
			fields: {
				type: Array,
				required: true,
			},
		},
	};
</script>
