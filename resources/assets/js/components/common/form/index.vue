<script setup>
	import { computed } from 'vue';
	import { useForm } from 'vee-validate';

	import useFormSubmission from '@/js/composables/form-submission';

	import FormInput from './Input';
	import FormRadio from './Radio';
	import FormCheckbox from './Checkbox';
	import FormSelect from './Select';
	import FormTextarea from './Textarea';

	const props = defineProps({
		action: {
			type: String,
			required: true,
		},

		values: {
			type: Object,
			default: null,
		},

		schema: {
			type: Array,
			default: null,
		},
	});

	const {
		submit,
		response,
		isSubmitting,
		fieldErrors,
		errorMessage,
	} = useFormSubmission();

	const {
		handleSubmit,
		// values,
		errors,
	} = useForm({
		initialValues: props.values || null,
	});

	const hasErrors = computed(() => !!Object.keys(errors.value).length);
	const onSubmit = handleSubmit((v) => submit(props.action, v));

	const fieldComponent = (as) => {
		const components = {
			input: FormInput,
			radio: FormRadio,
			checkbox: FormCheckbox,
			select: FormSelect,
			textarea: FormTextarea,
			submit: 'e-button',
		};

		return components[as] ?? FormInput;
	};
</script>

<script>
	export default {
		name: 'EForm',
	};
</script>

<template>
	<pre>
		frontend errors: {{ errors }}<br>
		server errors: {{ fieldErrors }}<br>
		server errorMessage: {{ errorMessage }}<br>
		hasErrors: {{ hasErrors }}<br>
	</pre>

	<pre
		v-if="response"
		v-text="response"
	/>

	<form
		v-else
		novalidate
		class="flex flex-col items-start gap-y-4"
		:disabled="isSubmitting"
		@submit="onSubmit"
	>
		<component
			:is="fieldComponent(as)"
			v-for="({ as, ...field }, index) in schema"
			:key="index"
			v-bind="field"
		/>
	</form>
</template>
