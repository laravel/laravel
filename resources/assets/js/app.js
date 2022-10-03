import { createApp } from 'vue';

import './etc/validators';

import UiButton from './components/common/UiButton';
import UiForm from './components/common/UiForm';
import UiIcon from './components/common/UiIcon';
import UiImg from './components/common/UiImg';
import UiPlaceholder from './components/common/UiPlaceholder';

const app = createApp({});

app.component('UiButton', UiButton);
app.component('UiForm', UiForm);
app.component('UiIcon', UiIcon);
app.component('UiImg', UiImg);
app.component('UiPlaceholder', UiPlaceholder);

app.mount('#app');
