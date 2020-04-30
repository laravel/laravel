import whatInput from 'what-input';

export default {
	methods: {
		isKeyboard() {
			return whatInput.ask() === 'keyboard';
		},
	},
};
