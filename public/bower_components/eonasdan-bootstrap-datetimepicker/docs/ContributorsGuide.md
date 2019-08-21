This guide is aimed to contributors wishing to understand the internals of the code in order to change/evolve the component. 

**Note:** this guide refers to **version 4** which is currently in beta and will be updated as we progress

## Introduction
This component consists actually of 2 subcomponent UI widgets one for the date and one for the time selection process. The developers can configure which of those are needed and also the granularity that the component will allow the users to select a date/time. Developers also choose the format that the selected datetime will be displayed in the input field.
The component uses on `jQuery`, `moment.js` and `bootstrap` libraries.

## Code
### Private variables

* `element` - Holds the DOM element this instance is attached to

* `options` - Holds an object with the curently set options for the specific instance of the component. Don't directly change the properties of that object use the public API methods instead. DO NOT expose this object or its properties outside of the component.

* `picker` - Reference variable to the created instance `(this)`

* `date` - Holds the moment object for the model value of the component. **DON'T** directly change this variable unless you **REALLY** know what you are doing. Use `setValue()` function to set it. It handles all component logic for updating the model value and emitting all the appropriate events

* `viewDate` - Holds the currently selected value that the user has selected through the widget. This is not the model value this is the view value. Changing this usually requires a subsequent call to `update()` function

* `unset` - A `boolean` variable that holds wheather the components model value is set or not. Model's value starts as `unset = true` and if is either set by the user or programmatically through the api to a valid value then it is set to `false`. If subsequent events lead to an invalid value then this variable is set to `true` again. Setting this variable usually takes place in the `setValue()` function.

* `input` - Hold the DOM input element this instance is attached to

* `component` - Holds a reference to the .input-group DOM element that the widget is attached or false if it is attached directly on an input field

* `widget` - Holds a reference to the DOM element containing the widget or `false` if the widget is hidden

* `use24hours` - Holds whether the component uses 24 hours format or not. This is initialized on the `format()` function

* `minViewModeNumber` - Holds the Numeric equivelant of the options.minViewMode parameter

* `format` - Holds the current format string that is used for formating the date model value. Note this is not the same thing as the `options.format` as the second could be set to `false` in which case the first takes the locale's `L` or `LT` value

* `currentViewMode` - Hold the state of the current viewMode for the DatePicker subcomponent

* `actions` - An object containing all the functions that can be called when the users clicks on the widget

* `datePickerModes` - An array of objects with configuration parameters for the different views of the DatePicker subcomponent

* `viewModes` - An array of strings containing all the possible strings that `options.viewMode` can take through `viewMode()` public api function

* `directionModes` - An array of strings containing all the possible strings that `options.direction` can take through `direction()` public api function

* `orientationModes` - An array of strings containing all the possible strings that `options.orientation` can take through `orientation()` public api function

### Private functions

#### Widget related

* `getDatePickerTemplate()` - returns a string containing the html code for the date picker subcomponent

* `getTimePickerTemplate()` - returns a string containing the html code for the time picker subcomponent

* `getTemplate()` - returns a string with containing the html code for all the DateTimePicker component

* `place()` - handles the placement of the widget's dropdown

* `updateMonths()` - updates the html subpage related to the months for Date picker view

* `updateYears()` - updates the html subpage related to the years for Date picker view

* `fillDate()` - updates the html subpage related to the days for Date picker view

* `fillHours()` - Creates the hours spans for the hours subview of the Time subcomponent

* `fillMinutes()` - Creates the minutes spans for the hours subview of the Time subcomponent

* `fillSeconds()` - Creates the seconds spans for the hours subview of the Time subcomponent

* `fillTime()` - Creates the main subview of the Time subcomponent

* `update()` - updates the UI of part of the widget

* `fillDow()` - Creates the day names in the days subview on the Date subcomponent

* `fillMonths()` - Creates the month spans for the months subview of the Date subcomponent

* `createWidget()` - creates the UI widget end attaches widget event listeners

* `destroyWidget()` - destroys the UI widget DOM element and detaches widget event listeners

* `showMode(dir)` - toggles between the various subpage related views of the DateTimePicker

#### Events related

* `notifyEvent(e)` - Use this function when you want to send en event to listener this could be used as a filter later

* `stopEvent(e)` - Shortcut for stopping propagation of events

* `doAction(e)` - Proxy function to call all the UI related click events

* `keydown(e)` - Function to trap 

* `change(e)` - Listener function to track change events occuring on the `input` dom element the component is attached to

* `attachDatePickerElementEvents()` - Attaches listeners to the existing DOM elements the component is attached to. Called upon construction of each datetimepicker instance

* `detachDatePickerElementEvents()` - Detaches listeners from the DOM element the component is attached to. Called on `destroy()`

* `attachDatePickerWidgetEvents()` - Attaches listeners on the components widget. Called on `show()`

* `detachDatePickerWidgetEvents()` - Detaches listeners on the components widget. Called on `hide()`

#### Model related

* `setValue(targetMoment)` - Sets the model value of the component takes a moment object. An `error` event will be emmited if the `targetMoment` does not pass the configured validations. Otherwise the `date` variable will be set and the relevant events will be fired.

* `isValid(targetMoment, granularity)` - returns `true` if the `targetMoment` moment object is valid according to the components set validation rules (`min/maxDates`, `disabled/enabledDates` and `daysOfWeekDisabled`). You may pass a second variable to check only up the the specific granularity `year, month, day, hour, minute, second`

#### Utilities

* `indexGivenDates (givenDatesArray)` - Function that takes the array from `enabledDates()` and `disabledDates()` public functions and stores them as object keys to enable quick lookup

* `isInEnableDates(date)` - Checks whether if the given moment object exists in the `options.enabledDates` object

* `isInDisableDates(date)` - Checks whether if the given moment object exists in the `options.disabledDates` array

* `dataToOptions()` - Parses `data-date-*` options set on the input dom element the component is attached to and returns an object with them

* `isInFixed()` - Checks if the dom element or its parents has a fixed position css rule.

* `parseInputDate(date)` - Parses a date parameter with moment using the component's `options.format` and `options.useStrict`. It returns a `moment` object or false if `parsedMoment#isValid()` returns `false`. Use this to parse date inputs from outside the component (public API calls).

* `init()` - Initializes the component. Called when the component instance is created
