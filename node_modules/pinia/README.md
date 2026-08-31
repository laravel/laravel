<p align="center">
  <a href="https://pinia.vuejs.org" target="_blank" rel="noopener noreferrer">
    <img width="180" src="https://pinia.vuejs.org/logo.svg" alt="Pinia logo">
  </a>
</p>
<br/>
<p align="center">
  <a href="https://npmx.dev/package/pinia"><img src="https://badgen.net/npm/v/pinia/^4" alt="npm package"></a>
  <a href="https://github.com/vuejs/pinia/actions/workflows/ci.yml"><img src="https://github.com/vuejs/pinia/actions/workflows/ci.yml/badge.svg" alt="build status"></a>
  <a href="https://codecov.io/gh/vuejs/pinia"><img src="https://codecov.io/gh/vuejs/pinia/branch/v4/graph/badge.svg?token=rU2xxQ6BGH"/></a>
</p>
<br/>

# Pinia

> Intuitive, type safe and flexible Store for Vue

- 💡 Intuitive
- 🔑 Type Safe
- ⚙️ Devtools support
- 🔌 Extensible
- 🏗 Modular by design
- 📦 Extremely light
- ⛰️ Nuxt Module

The latest version of pinia works with Vue 3. See the branch [v2](https://github.com/vuejs/pinia/tree/v2) for a version that works with Vue 2.

Pinia is the most similar English pronunciation of the word _pineapple_ in Spanish: _piña_. A pineapple is in reality a group of individual flowers that join together to create a multiple fruit. Similar to stores, each one is born individually, but they are all connected at the end. It's also a delicious tropical fruit indigenous to South America.

## 👉 [Demo with Vue 3 on StackBlitz](https://stackblitz.com/github/piniajs/example-vue-3-vite)

## 👉 [Demo with Nuxt 3 on StackBlitz](https://stackblitz.com/github/piniajs/example-nuxt-3)

## Help me keep working on this project 💚

- [Become a Sponsor on GitHub](https://github.com/sponsors/posva)
- [One-time donation via PayPal](https://paypal.me/posva)

<!--sponsors start-->

<h4 align="center">Gold Sponsors</h4>
<p align="center">
    <a href="https://www.coderabbit.ai/?utm_source=vuerouter&utm_medium=sponsor" target="_blank" rel="noopener noreferrer">
    <picture>
      <source srcset="https://posva-sponsors.pages.dev/logos/coderabbitai-dark.svg" media="(prefers-color-scheme: dark)" height="72px" alt="CodeRabbit" />
      <img src="https://posva-sponsors.pages.dev/logos/coderabbitai-light.svg" height="72px" alt="CodeRabbit" />
    </picture>
  </a>
</p>

<h4 align="center">Silver Sponsors</h4>
<p align="center">
    <a href="https://www.vuemastery.com/" target="_blank" rel="noopener noreferrer">
    <picture>
      <source srcset="https://posva-sponsors.pages.dev/logos/vuemastery-dark.png" media="(prefers-color-scheme: dark)" height="42px" alt="VueMastery" />
      <img src="https://posva-sponsors.pages.dev/logos/vuemastery-light.svg" height="42px" alt="VueMastery" />
    </picture>
  </a>
    <a href="https://www.controla.ai/?utm_source=posva" target="_blank" rel="noopener noreferrer">
    <picture>
      <source srcset="https://posva-sponsors.pages.dev/logos/controla-dark.png" media="(prefers-color-scheme: dark)" height="42px" alt="Controla" />
      <img src="https://posva-sponsors.pages.dev/logos/controla-light.png" height="42px" alt="Controla" />
    </picture>
  </a>
    <a href="https://jobs.sendcloud.com" target="_blank" rel="noopener noreferrer">
    <picture>
      <source srcset="https://posva-sponsors.pages.dev/logos/sendcloud-dark.svg" media="(prefers-color-scheme: dark)" height="42px" alt="SendCloud" />
      <img src="https://posva-sponsors.pages.dev/logos/sendcloud-light.svg" height="42px" alt="SendCloud" />
    </picture>
  </a>
</p>

<h4 align="center">Bronze Sponsors</h4>
<p align="center">
    <a href="https://www.rtvision.com/" target="_blank" rel="noopener noreferrer">
    <picture>
      <source srcset="https://avatars.githubusercontent.com/u/8292810" media="(prefers-color-scheme: dark)" height="26px" alt="RTVision" />
      <img src="https://avatars.githubusercontent.com/u/8292810" height="26px" alt="RTVision" />
    </picture>
  </a>
    <a href="https://storyblok.com" target="_blank" rel="noopener noreferrer">
    <picture>
      <source srcset="https://posva-sponsors.pages.dev/logos/storyblok.png" media="(prefers-color-scheme: dark)" height="26px" alt="Storyblok" />
      <img src="https://posva-sponsors.pages.dev/logos/storyblok.png" height="26px" alt="Storyblok" />
    </picture>
  </a>
</p>

<!--sponsors end-->
<!--sponsors end-->

---

## FAQ

A few notes about the project and possible questions:

**Q**: _Is Pinia the successor of Vuex?_

**A**: [Yes](https://vuejs.org/guide/scaling-up/state-management.html#pinia)

**Q**: _What about dynamic modules?_

**A**: Dynamic modules are not type safe, so instead [we allow creating different stores](https://pinia.vuejs.org/cookbook/composing-stores.html) that can be imported anywhere

## Installation

```bash
# or pnpm or yarn
npm install pinia @vue/devtools-api
```

## Usage

### Install the plugin

Create a pinia (the root store) and pass it to app:

```js
// Vue 3
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'

const pinia = createPinia()
const app = createApp(App)

app.use(pinia)
app.mount('#app')
```

For more detailed instructions, including [Nuxt configuration](https://pinia.vuejs.org/ssr/nuxt.html), check the [Documentation](https://pinia.vuejs.org).

### Create a Store

You can create as many stores as you want, and they should each exist in different files:

```ts
import { defineStore } from 'pinia'

// main is the name of the store. It is unique across your application
// and will appear in devtools
export const useMainStore = defineStore('main', {
  // a function that returns a fresh state
  state: () => ({
    counter: 0,
    name: 'Eduardo',
  }),
  // optional getters
  getters: {
    // getters receive the state as first parameter
    doubleCounter: (state) => state.counter * 2,
    // use getters in other getters
    doubleCounterPlusOne(): number {
      return this.doubleCounter + 1
    },
  },
  // optional actions
  actions: {
    reset() {
      // `this` is the store instance
      this.counter = 0
    },
  },
})
```

`defineStore` returns a function that has to be called to get access to the store:

```ts
import { useMainStore } from '@/stores/main'
import { storeToRefs } from 'pinia'

export default defineComponent({
  setup() {
    const main = useMainStore()

    // extract specific store properties
    const { counter, doubleCounter } = storeToRefs(main)

    return {
      // gives access to the whole store in the template
      main,
      // gives access only to specific state or getter
      counter,
      doubleCounter,
    }
  },
})
```

## Documentation

To learn more about Pinia, check [its documentation](https://pinia.vuejs.org).

## License

[MIT](http://opensource.org/licenses/MIT)
