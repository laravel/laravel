<script setup>
	import { ref, computed } from 'vue';
	import { useForm } from 'vee-validate';

	import useFormSubmission from '@/js/composables/formSubmission';

	import FormInput from './FormInput';
	import FormRadio from './FormRadio';
	import FormCheckbox from './FormCheckbox';
	import FormSelect from './FormSelect';
	import FormTextarea from './FormTextarea';

	const props = defineProps({
		action: {
			type: String,
			required: true,
		},

		schema: {
			type: Array,
			default: null,
		},

		values: {
			type: Object,
			default: null,
		},
	});

	const wrapper = ref(null);

	const {
		handleSubmit,
		setErrors,
		// errors,
	} = useForm({
		initialValues: props.values || null,
	});

	const {
		submit,
		response,
		isSubmitting,
		errorMessage,
	} = useFormSubmission({
		wrapper: computed(() => wrapper.value || document),
		skipFalsy: false,
		setErrors,
	});

	// const hasErrors = computed(() => !!Object.keys(errors.value).length);
	const onSubmit = handleSubmit((v) => submit(props.action, v));

	const fieldComponent = (as) => {
		const components = {
			input: FormInput,
			radio: FormRadio,
			checkbox: FormCheckbox,
			select: FormSelect,
			textarea: FormTextarea,
			submit: 'ui-button',
		};

		return components[as] ?? FormInput;
	};
</script>

<script>
	export default {
		name: 'UiForm',
	};
</script>

<template>
	<div ref="wrapper">
		<pre
			v-if="response"
			class="e-thank-you"
			v-text="response"
		/>

		<form
			v-else
			novalidate
			class="flex flex-col items-start gap-y-5"
			:disabled="isSubmitting"
			@submit="onSubmit"
		>
			<pre
				v-if="errorMessage"
				class="e-h4 text-red"
				v-text="errorMessage"
			/>

			<component
				:is="fieldComponent(as)"
				v-for="({ as, ...field }, index) in schema"
				:key="index"
				v-bind="field"
			/>
		</form>
	</div>
</template>
