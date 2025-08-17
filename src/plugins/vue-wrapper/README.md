# Vue wrapper for Comet Components

A simple package that provides the compiled versions of Vue 3
and [Vue SFC loader](https://github.com/FranckFreiburger/vue3-sfc-loader) as a Composer dependency, for use as
ES6 modules in Comet Components.

## Updating

The `postinstall` script in the Core `package.json` will automatically download the latest builds of Vue (dev and prod) and put them in the `packages/core/src/plugins/vue-wrapper/src` directory. This should automatically happen when you run `npm install` in the Core directory.

Alternatively, updated versions of the compiled files can be found at:

- [Vue dist on Unpkg](https://unpkg.com/browse/vue@3.5.13/dist/) (then select version from the dropdown)
- [Vue SFC Loader on NPM](https://www.npmjs.com/package/vue3-sfc-loader?activeTab=code)
