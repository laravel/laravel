## Functions

<div class="alert alert-info">
    <strong>Note</strong>
    All functions are accessed via the <code>data</code> attribute e.g. <code>$('#datetimepicker').data("DateTimePicker").FUNCTION()</code>
</div>

###destroy()

Destroys the widget and removes all attached event listeners

----------------------

### toggle()

Shows or hides the widget

#### Emits

* `dp.hide` - if the widget is hidden after the toggle call

* `dp.show` - if the widget is show after the toggle call

* `dp.change` - if the widget is opened for the first time and the input element is empty and `options.useCurrent != false`

----------------------

### show()

Shows the widget

#### Emits

* `dp.show` - if the widget was hidden before that call

* `dp.change` - if the widget is opened for the first time and the useCurrent is set to true or to a granularity value and the input element the component is attached to has an empty value

----------------------

### hide()

Hides the widget

#### Emits

* `dp.hide` - if the widget was visible before that call

----------------------

### disable()

Disables the input element, the component is attached to, by adding a `disabled="true"` attribute to it. If the widget was visible before that call it is hidden.

#### Emits

* `dp.hide` - if the widget was visible before that call

----------------------

### enable()

Enables the input element, the component is attached to, by removing `disabled` attribute from it.

----------------------

### clear()

Clears the datepicker by setting the value to `null`

### viewDate

<small>4.14.30</small> Issue #872

#### viewDate()

Returns a `moment` variable with the currently set `options.viewDate` option.

#### viewDate(viewDate)

Takes a `string, moment or Date` value.

This will change the `viewDate` without changing or setting the selected date.