# FAQs

# How do I disable the date or time element
<small>How do I format ...; How do I add seconds; etc.</small>

The picker uses the `format` option to decide what components to show. Set `format` to `LT`, `LTS` or another valid [MomentJs format string](http://momentjs.com/docs/#/displaying/format/) to display certain components

# How do I change the language/locale

The picker uses MomentJs to determine the language string. You can use `moment-with-locales` or you can include whatever local file you need. Set the picker's `locale` option to `de` or whatever the locale string is.

# How do I change the styles? The picker closes.

Set `debug:true` which will force the picker to stay open, even `onBlur`. You can hide the picker manually by calling `hide()`

# How do I change the start of the week?

Start of the week is based on the [`locale` provided](Options.md#locale). This is defined by moment's locales. If you want to change it, create your own locale file or override. [See moment's docs](http://momentjs.com/docs/#/i18n/).

# How I use the picker as birthday picker?

Use the [`viewMode`](Options.md#viewmode) option to `'years'`