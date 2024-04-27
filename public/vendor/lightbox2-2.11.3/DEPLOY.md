## How to Make a Release

### Build
- **Checkout dev branch.** This will contain work queued up for the next release.
- **Update version number.** Manually update version number in `src/lightbox.js` and `package.json`. Don't use `npm version`.
- **Build and merge to `master`.** Run `grunt build`. Make sure you have run `bower install` ahead of this as it will pull down jQuery which is utilized in the build step. Push to `master`.

### Release
- **Create tagged release.** Go to [Github Releases page](https://github.com/lokesh/lightbox2/releases). Draft a new release. Naming convention is `v2.8.1`. Add notes that link to PRs.
- **`npm publish`**. No need to do anything for Bower as it is entirely based on the Github repo.

### Maintenance and Docs
- **GH clean-up.** Close out issues with `[status] pending release`.
- **Lightbox Site.** If there are any changes to the options, don't forget to update the [Lightbox Site](http://localhost:8000/dist/#options). The code lives in a separate repo, [lightbox2-site](https://github.com/lokesh/lightbox2-site/).
