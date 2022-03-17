import { createApp } from 'vue';

import './etc/validators';

import Button from './components/common/Button';
import Form from './components/common/form';
import Icon from './components/common/Icon';
import Placeholder from './components/common/Placeholder';

const app = createApp({});

app.component('EButton', Button);
app.component('EForm', Form);
app.component('EIcon', Icon);
app.component('EPlaceholder', Placeholder);

app.mount('#app');
