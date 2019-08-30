<template>
	<div>
		<e-label
			v-if="label"
			:for="id"
			:text="label"
		/>

		<div class="select">
			<select
				:id="id"
				v-model="form.select"
				class="input"
			>
				<option
					disabled
					:value="null"
					v-html="$options.filters.trans('app.form.placeholders.select')"
				/>

				<option
					v-for="(option) in options"
					:key="option.name"
					:value="option.value"
					v-html="option.name"
				/>
			</select>

			<icon class="select__icon" name="chevron-down" />
		</div>

		<error-text :errors="error" />
	</div>
</template>

<script>
	import ErrorText from './error-text.vue';

	export default {
		components: {
			'error-text': ErrorText,
		},

		props: {
			id: {
				type: String,
				required: true,
			},

			label: {
				type: String,
				default: null,
			},

			options: {
				type: Array,
				default: () => {},
			},

			error: {
				type: Object,
				default: () => {},
			},
		},

		data() {
			return {
				form: {
					select: null,
				},
			};
		},
	};
</script>
