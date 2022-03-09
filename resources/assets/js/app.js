import { createApp } from 'vue';

import '@/js/etc/validators';

import Form from './components/common/form';
import Button from './components/common/Button';
import Icon from './components/common/Icon';
import Placeholder from './components/common/Placeholder';

const app = createApp({});

app.component('EForm', Form);
app.component('EButton', Button);
app.component('EPlaceholder', Placeholder);
app.component('EIcon', Icon);

app.mount('#app');
