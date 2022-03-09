import { configure, defineRule } from 'vee-validate';
import { required, email } from '@vee-validate/rules';
import { localize } from '@vee-validate/i18n';

defineRule('required', required);
defineRule('email', email);

configure({
	generateMessage: localize('en', {
		messages: {
			required: 'The {field} field is required!',
			email: 'Please enter a valid email adress!',
		},
	}),
});
