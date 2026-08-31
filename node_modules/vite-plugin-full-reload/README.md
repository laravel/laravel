<h2 align='center'><samp>vite-plugin-full-reload</samp></h2>

<p align='center'>Automatically reload the page when files are modified</p>

<p align='center'>
  <a href='https://www.npmjs.com/package/vite-plugin-full-reload'>
    <img src='https://img.shields.io/npm/v/vite-plugin-full-reload?color=222&style=flat-square'>
  </a>
  <a href='https://github.com/ElMassimo/vite-plugin-full-reload/blob/main/LICENSE.txt'>
    <img src='https://img.shields.io/badge/license-MIT-blue.svg'>
  </a>
</p>

<br>

[vite-plugin-full-reload]: https://github.com/ElMassimo/vite-plugin-full-reload
[vite-plugin-live-reload]: https://github.com/arnoson/vite-plugin-live-reload
[Vite Ruby]: https://github.com/ElMassimo/vite_ruby
[JS From Routes]: https://github.com/ElMassimo/js_from_routes
[picomatch]: https://github.com/micromatch/picomatch#globbing-features

## Why? 🤔

When using _[Vite Ruby]_, I wanted to see changes to server-rendered layouts and templates without having to manually reload the page.

Also, in _[JS From Routes]_ path helpers are generated when Rails reload is triggered.

Triggering a page reload when `config/routes.rb` is modified makes the DX very smooth.

## Installation 💿

Install the package as a development dependency:

```bash
npm i -D vite-plugin-full-reload # yarn add -D vite-plugin-full-reload
```

## Usage 🚀

Add it to your plugins in `vite.config.ts`

```ts
import { defineConfig } from 'vite'
import FullReload from 'vite-plugin-full-reload'

export default defineConfig({
  plugins: [
    FullReload(['config/routes.rb', 'app/views/**/*'])
  ],
})
```

This is useful to trigger a page refresh for files that are not being imported, such as server-rendered templates.

To see which file globbing options are available, check [picomatch].

## Configuration ⚙️

The following options can be provided:

- <kbd>root</kbd>
  
  Files will be resolved against this directory.

  __Default:__ `process.cwd()`

  ``` js
  FullReload('config/routes.rb', { root: __dirname }),
  ``` 

- <kbd>delay</kbd>

  How many milliseconds to wait before reloading the page after a file change.
  It can be used to offset slow template compilation in Rails.

  __Default:__ `0`
  
  ```js
  FullReload('app/views/**/*', { delay: 100 })
  ``` 

- <kbd>always</kbd>

  Whether to refresh the page even if the modified HTML file is not currently being displayed.

  __Default:__ `true`
  
  ```js
  FullReload('app/views/**/*', { always: false })
  ``` 

## Acknowledgements

- <kbd>[vite-plugin-live-reload]</kbd>

  This is a nice plugin, I found it right before publishing this one.

  I've made [two](https://github.com/arnoson/vite-plugin-live-reload/pull/3) [PRs](https://github.com/arnoson/vite-plugin-live-reload/pull/5) that were needed to support these use cases.

  At this point in time they are very similar, except this library doesn't create another `chokidar` watcher.

## License

This library is available as open source under the terms of the [MIT License](https://opensource.org/licenses/MIT).
