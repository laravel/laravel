# Version 4

## 4.17.42

### Bug Squashing

* fixed moment dependencies to all be the same
* defaulted `option.timeZone` to `''` instead of UTC. This way it will default to the local timezone if it's not set.
* fixed #959
* fixed #1311 internal `getMoment` function no longer sets `startOf('d')`
* fixed #935

### Other

* moved some (will move the rest soon) inline docs to JSDoc now that ReSharper supports it.
* moved getter/setter functions to options page instead. #1313

## 4.17.37

### New Features

* Momentjs TZ intergration #1242 thanks @bodrick
* Independent CSS file, in case you don't want bootstrap for some reason

### Bug Squashing

* Slight changes decade view
* Moved all tooltip text to `tooltips`
* fixed #1212

## 4.15.35

### New Features

`tooltips` allows custom, localized text to be included for icon tooltips

### Bug Squashing

fixed #1066

fixed #1087 `sideBySide` properly supports `toolbarPlacement [top, bottom]`

fixed #1119 

fixed #1069 added input.blur()

fixed #1049 fixed doc example 

fixed #999 picker now looks for an element with `.input-group-addon`


## 4.14.30

### New Features

`disabledTimeIntervals` #644

`allowInputToggle` #929

`focusOnShow` #884

public `viewDate` function #872

`enabledHours` and `disabledHours`.

`dp.update` fires when `viewDate` is changed (in most cases) #937

`viewMode` now supports a decades view. 

   **Note**: because the year picker shows 12 years at a time, I've elected to make this view show blocks of 12 years

   **Note**: when selecting a decade the `viewDate` will change to the **center** of the selected years

`parseInputDate` #1095

### Bug Squashing

fixed #815 by adding `.wider` when using both seconds and am/pm.

fixed #816 changed both min/max date to move the selected date inside.

fixed #855 #881 `fillDate`, `fillMonths`, `fillDow` uses `startOf('day')`, which will hopefully fix the DST issues.

fixed #885 `daysOfWeekDisabled` will move the date to a valid date if `useCurrent` is `true`. Today button will check if the DoW is disabled.

fixed #906

fixed #912 if `useCurrent:false` month and year view will no longer have the current month/year selected.

fixed #914 `use24hours` will ignore anything in side of `[]` in the format string.

fixed #916 added titles to all icons. At some point the text should be moved to the icon's array, but this would probably be a breaking change.

fixed #940 added -1 tab index to am/pm selector

### Other Changes

changed in/decrement behavior to check if the new date is valid at that granularity (hours, minutes, seconds). will also validate as before

## 4.7.14

Added several in new features:
    
    `keybinds`, `inline`, `debug`, `clear()`, `showClose`, `ingoreReadOnly`, `datepickerInput` and `keepInvalid`.

Bug squashing

## 4.0.0

#### Changes for using the component

* Defined a [Public API](https://github.com/Eonasdan/bootstrap-datetimepicker/wiki/Version-4-Public-API) and hidden rest of functions, variables so that all configuration options can be changed dynamically.

* `set/getDate()` is now replaced with an overloaded `date()` function. Use it without a parameter to get the currently set date or with a parameter to set the date.

* `hide()`, `show()`, `toggle()`, `enable()`, `disable()` and the rest of setter functions now support chaining. ie `$('#id').data('DateTimePicker').format('DD-MM-YYYY').minDate(moment()).defaultDate(moment()).show()` works

* Replaced previous - next buttons in Date subviews with configurable icons

* Changed `language` option name to `locale` to be inline with moment naming

* Implemented #402 all data-date-* variables are more readable and also match with the ones in the configuration object

* `options.direction` and `options.orientation` were merged into a single object `options.widgetPositioning` with `vertical` and `horizontal` keys that take a string value of `'auto', 'top', 'bottom'` and `'auto', 'left', 'right'` respectively. Note that the `'up'` option was renamed to `'top'`

#### Added functionality

* added a second way to define options as data attributes. Instead of adding distinct `data-date-*` config options you can now also pass a `data-date-options` attribute containing an object just the same as the options object that `element.datetimepicker` constructor call takes

* also added a `options()` public api function to get/set that takes an option object and applies it to the component in one call

* Implemented [#130](https://github.com/Eonasdan/bootstrap-datetimepicker/issues/130) by introducing a boolean `options.calendarWeeks` and `calendarWeeks()` api function

* Implemented [#328](https://github.com/Eonasdan/bootstrap-datetimepicker/issues/328), [#426](https://github.com/Eonasdan/bootstrap-datetimepicker/issues/426)

* Implemented [#432](https://github.com/Eonasdan/bootstrap-datetimepicker/issues/432). Widget DOM element is now lazily added only when shown and removed from the document when hidden.

* Implemented [#141](https://github.com/Eonasdan/bootstrap-datetimepicker/issues/141) and [#283](https://github.com/Eonasdan/bootstrap-datetimepicker/issues/283)


#### Contributors related internal code changes

* Refactor all UI click functions and put them as functions in the actions array private variable

* Refactor template building process to seperate functions according to what they do

* Remove some styles that where hardcoded in the javascript code

* Refactor all code that changes the picker.date to change it through the setValue function to allow one place for validation logic (min/max/weekdaysenabled etc) and also one place for emmiting dp.change events

* The v4beta branch code includes all fixes up to v.3.1.2

* Added `toggle()` to the public API which toggles the visibility of the DateTimePicker

* Refactored set function to be included in the setValue function

* Added a testing framework using jasmine and phantom.js

# Version 3

## 3.0.0


* Fix for #170, #179, #183: Changed event to `dp.-`. This should fix the double change event firing.
* Fix for #192: `setDate` now fires `dp.change`
* Fix for #182: Picker will **not** set the default date if the input field has a value
* Fix for #169: Seconds doesn't get reset when changing the date (Thanks to PR #174)
* Fix for #168 z-index fix for BS modal
* Fix for #155 Picker properly displays the active year and month
* Fix for #154 CSS update to fix the collapse jump
* Fix for #150 and #75 `minViewMode` and `viewMode` work properly
* Fix for #147 AM/PM won't toggle when selecting a value from the hours grid
* Fix for #44 Finally! It's here!! Thanks to @ruiwei and his code on #210 picker will adjust the positioning of the widget.

#### Manually merged PR

* PR #178 When using `minuteStepping` the minute select grid will only show available steppings
* PR #195, #197 Using the `data-OPTION` has been changed to `data-date-OPTION`. These options are expected to be on the `input-group` if you're using the `input-group` **or** the a bare input field if you're not using the `input-group`
* PR #184 The option `sideBySide` change be used to display both the d and the timepicker side by side
* PR #143 Added option `daysOfWeekDisabled: []`. For example, use `daysOfWeekDisabled: [0,6]` to disable Sunday and Saturday

#### **Other Changes**
* Changed picker width to 300px if using seconds and am/pm
* Added option `useCurrent`, thanks to @ruiwei. When true, picker will set the value to the current date/time (respects picker's format)
* Added option `showToday`, thanks to @ruiwei. When true, picker will display a small arrow to indicate today's date.
* Changed `startDate` to `minDate` and `endDate` to `maxDate` to make it more clear what these options do.

# Version 2

#### 2.1.32 (Hotfix)

* Fix for #151: When a bad date value or the picker is cleared, the plugin will not longer attempt to reset it back to the previous date
* Fix for #140: `setDate` can be given `null` to force clear the picker

#### 2.1.30
##### Important! `build.less` file name has been been changed to `bootstrap-datetimepicker-build.less` to prevent collisions

* Fix for #135: `setStartDate` and `setEndDate` should now properly set.
* Fix for #133: Typed in date now respects en/disabled dates
* Fix for #132: En/disable picker function works again
* Fix for #117, #119, #128, #121: double event `change` event issues should be fixed
* Fix for #112: `change` function no longer sets the input to a blank value if the passed in date is invalid

* Enhancement for #103: Increated the `z-index` of the widget

#### 2.1.20
* Fix for #83: Changes to the picker should fire native `change` event for knockout and the like as well as `change.dp` which contains the old date and the new date
* Fix for #78: Script has been update for breaking changes in Moment 2.4.0
* Fix for #73: IE8 should be working now

* Enhancement for #79: `minuteStepping` option takes a number (default is 1). Changing the minutes in the time picker will step by this number.
* Enhancement for #74 and #65: `useMinutes` and `useSeconds` are now options. Disabling seconds will hide the seconds spinner. Disabling minutes will display `00` and hide the arrows
* Enhancement for #67: Picker will now attempt to convert all `data-OPTION` into its appropriate option

#### 2.1.11
* Fix for #51, #60
* Fix for #52: Picker has its own `moment` object since moment 2.4.0 has removed global reference
* Fix for #57: New option for `useStrict`. When validating dates in `update` and `change`, the picker can use a stricter formatting validation
* Fix for #61: Picker should now properly take formatted date. Should also have correct start of the week for locales.
* Fix for #62: Default format will properly validate time picker only.

#### 2.1.5
* Custom icons, such as Font Awesome, are now supported. (#49)
* If more then one `input-group-addon` is present use `datepickerbutton` to identify where the picker should popup from. (#48)
* New Event: `error.dp`. Fires when Moment cannot parse the date or when the timepicker cannot change because of a `disabledDates` setting. Returns a Moment date object. The specific error can be found be using `invalidAt()`. For more information see [Moment's docs](http://momentjs.com/docs/#/parsing/is-valid/)
* Fix for #42, plugin will now check for `A` or `a` in the format string to determine if the AM/PM selector should display.
* Fix for #45, fixed null/empty and invalid dates
* Fix for #46, fixed active date highlighting
* Fix for #47, `change.dp` event to also include the previous date.

####2.0.1
* New event `error.dp` fires when plugin cannot parse date or when increase/descreasing hours/minutes to a disabled date.
* Minor fixes

####2.0.0
* `disabledDates` is now an option to set the disabled dates. It accepts date objects like `new Date("November 12, 2013 00:00:00")` and `12/25/2013' and `moment` date objects
* Events are easier to use