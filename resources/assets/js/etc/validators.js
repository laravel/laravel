import { configure, defineRule } from 'vee-validate';
import AllRules from '@vee-validate/rules';
import { localize } from '@vee-validate/i18n';

import messages from 'assets/../lang/en-validation.json';

Object.keys(AllRules).forEach((rule) => {
	defineRule(rule, AllRules[rule]);
});

configure({
	generateMessage: localize('en', { messages }),
});
