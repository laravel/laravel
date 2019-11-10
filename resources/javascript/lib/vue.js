import Vue from 'vue'

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */
const files = require.context('./../components/', true, /\.vue$/i);

files
  .keys()
  .map(key => {
    Vue.component(
      key
        .split('/')
        .pop()
        .split('.')[0],
      files(key).default
    )
  })

export default Vue
