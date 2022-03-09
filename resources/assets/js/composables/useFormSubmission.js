import { reactive, toRefs } from 'vue';

export default function useFormSubmission() {
	const data = reactive({
		isSubmitting: false,
		response: null,
		errorMessage: null,
		errors: null,
	});

	const csrfToken = () => {
		const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

		if (!token) {
			console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
		}

		return token;
	};

	const onSubmitSuccess = async (response) => {
		const { redirect, response: resp } = await response.json();

		if (redirect) {
			window.location = redirect;
		} else if (resp) {
			data.response = resp;
		}
	};

	const onSubmitFailure = async (response) => {
		if (response.status !== 422) {
			throw new Error(`Unexpected response status: ${response.status}`);
		}

		const { errors = {}, message } = await response.json();

		data.errorMessage = message;
		data.errors = errors;
	};

	const submit = async (action, values) => {
		data.isSubmitting = true;

		const body = new FormData();

		Object.keys(values)
			.forEach((key) => body.append(key, values[key]));

		try {
			const response = await fetch(action, {
				method: 'POST',
				headers: {
					'X-Requested-With': 'XMLHttpRequest',
					'X-CSRF-TOKEN': csrfToken(),
				},
				body,
			});

			if (!response.ok) {
				throw response;
			}

			onSubmitSuccess(response);
		} catch (error) {
			onSubmitFailure(error);
		} finally {
			data.isSubmitting = false;
		}
	};

	return {
		submit,
		...toRefs(data),
	};
}
