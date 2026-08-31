# vue-router [![nmp version](https://img.shields.io/npm/v/vue-router.svg)](https://npmx.dev/package/vue-router) [![test](https://github.com/vuejs/router/actions/workflows/test.yml/badge.svg)](https://github.com/vuejs/router/actions/workflows/test.yml) [![codecov](https://codecov.io/gh/vuejs/router/graph/badge.svg?token=azNM3FI0d1)](https://codecov.io/gh/vuejs/router)

> To see what versions are currently supported, please refer to the [Security Policy](./packages/router/SECURITY.md).

<h2 align="center">Supporting Vue Router</h2>

Vue Router is part of the Vue Ecosystem and is an MIT-licensed open source project with its ongoing development made possible entirely by the support of Sponsors. If you would like to become a sponsor, please consider:

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

---

Get started with the [documentation](https://router.vuejs.org).

## Quickstart

- In-browser [playground](https://play.vuejs.org/#eNqlVG1v2jAQ/iu3dBJUg5iyTZqyFLWrqnXTXqpu2pdlH0JiIG1iW7ZDqVD++8523ii0XyoEOHfP3T13z8Vbr4gz5t8qL/CyQnCpYQuJpLGm50JABQvJCxisSzqIWAuQvNRUtl6fOEMHMbGNLxbCd/ERazMP8XscMQC/VHTowt1zwUumh4MjDBsceyOvDkd+oaaFyDF+ZoDh6mR2RfOcm2KvQoKP1izsHx6UlpwtZxellJRpxxlErFdBSGofbLfw2jr8RZnn1+iEqrJpiMsTsnjdJLyxLL9l7A40P408EnmzzxzPcMULGpLO/2REPEdLG3ZunvbiQtLUDI02u7n+ZPQeSI2r3SFpB4PzcrP09QFFv9OCy4erTGn8G9VGl7ev9LhVs9XTNGhLN6I2hlrZRnbT0A6utbQrQDcWmnCmalEknO5wGW5NeytHMzjEfXg8MhAbrQL460a0derCgAywOY6UGAofdOQrG9UHWj120V0PDv4PfytcTRxtv2mzjyqRmdCgqC4F5DFbosRaobxGE+fEIz48WtzprMmEazvdU3BnZi+r06Y6WMipNi5igRcAZ1jKTj6qHVghAGsxtm4xjDnyVloLFRBSMnG39HGApEOcvScpKtWz+FQV47nk9wrPt5i5liLyzhBEUrrWnOdqHIvsqfR7wLMP/ok/7Sr1fXv1TDl8sytsWytcvkW2fNS02YEsp/Kn0Bku507zMd4z91+tTcuStuSTFU3uDthv1ca1cS0pMljTXsM6lkuKl4BxX/76QTd4bp0FT8sc0c84b6jieWk4OtinkqVIu4ezbL9YDTO2/K0uN5oy1TRliNppWLwV9uKZ1ju6b/13vSkq/ZBT5SfKXDR4Y43AXEcubs5lSmUAU7EBJJulcDSZTD4aV4HpMjaec615EcDJRGysXcRpimRbC1aJGKaFGcTwBr82cR2d0wW+qX3katqv3KXvCCRJ0iMQwAQ/0zqDV/0H+yxf5Q==)
- Add it to an existing Vue Project:

  ```bash
  npm install vue-router@5
  ```

## Contributing

See [Contributing Guide](https://github.com/vuejs/router/blob/main/.github/contributing.md).

## Special Thanks

<a href="https://www.browserstack.com">
  <img src="https://github.com/vuejs/vue-router/raw/dev/assets/browserstack-logo-600x315.png" height="80" title="BrowserStack Logo" alt="BrowserStack Logo" />
</a>

Special thanks to [BrowserStack](https://www.browserstack.com) for letting the maintainers use their service to debug browser specific issues.
