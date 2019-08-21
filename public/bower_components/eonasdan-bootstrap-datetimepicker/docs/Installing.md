# Minimal Requirements

1. jQuery
2. Moment.js
3. Bootstrap.js (transition and collapse are required if you're not using the full Bootstrap)
4. Bootstrap Datepicker script
5. Bootstrap CSS
6. Bootstrap Datepicker CSS
7. Locales: Moment's locale files are [here](https://github.com/moment/moment/tree/master/locale)

# Installation Guides
* [Bower](#bower-)
* [Nuget](#nuget)
* [Rails](#rails-)
* [Angular](#angular-wrapper)
* [Meteor.js](#meteorjs)
* [Manual](#manual)

## [bower](http://bower.io) ![Bower version](https://badge.fury.io/bo/eonasdan-bootstrap-datetimepicker.png)

Run the following command:
```
bower install eonasdan-bootstrap-datetimepicker#latest --save
```

Include necessary scripts and styles:
```html
<head>
  <!-- ... -->
  <script type="text/javascript" src="/bower_components/jquery/jquery.min.js"></script>
  <script type="text/javascript" src="/bower_components/moment/min/moment.min.js"></script>
  <script type="text/javascript" src="/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="/bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
  <link rel="stylesheet" href="/bower_components/bootstrap/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="/bower_components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css" />
</head>
```
## Nuget
### [LESS](https://www.nuget.org/packages/Bootstrap.v3.Datetimepicker/): ![NuGet version](https://badge.fury.io/nu/Bootstrap.v3.Datetimepicker.png)
```
PM> Install-Package Bootstrap.v3.Datetimepicker
```

### [CSS](https://www.nuget.org/packages/Bootstrap.v3.Datetimepicker.CSS/): ![NuGet version](https://badge.fury.io/nu/Bootstrap.v3.Datetimepicker.CSS.png)
```
PM> Install-Package Bootstrap.v3.Datetimepicker.CSS
```

```html
<head>
  <script type="text/javascript" src="/scripts/jquery.min.js"></script>
  <script type="text/javascript" src="/scripts/moment.min.js"></script>
  <script type="text/javascript" src="/scripts/bootstrap.min.js"></script>
  <script type="text/javascript" src="/scripts/bootstrap-datetimepicker.*js"></script>
  <!-- include your less or built css files  -->
  <!-- 
  bootstrap-datetimepicker-build.less will pull in "../bootstrap/variables.less" and "bootstrap-datetimepicker.less";
  or
  <link rel="stylesheet" href="/Content/bootstrap-datetimepicker.css" />
  -->
</head>
```

## [Rails](http://rubygems.org/gems/bootstrap3-datetimepicker-rails) ![Gem Version](https://badge.fury.io/rb/bootstrap3-datetimepicker-rails.png)

Add the following to your `Gemfile`:
```ruby
gem 'momentjs-rails', '>= 2.9.0'
gem 'bootstrap3-datetimepicker-rails', '~> 4.14.30'
```
Note: You may need to change the version number above to the version number on the badge above.
Read the rest of the install instructions @ 
[TrevorS/bootstrap3-datetimepicker-rails](https://github.com/TrevorS/bootstrap3-datetimepicker-rails)


## Angular Wrapper
Follow the link [here](https://gist.github.com/eugenekgn/f00c4d764430642dca4b)

## Meteor.js

This widget has been package for the [Meteor.js](http://www.meteor.com/) platform, to install it use meteorite as follows:

`$ mrt add tsega:bootstrap3-datetimepicker`

For more detail see the package page on [Atmosphere](http://atmospherejs.com/package/bootstrap3-datetimepicker)

## Manual

### Acquire [jQuery](http://jquery.com)
### Acquire  [Moment.js](https://github.com/moment/moment)
### Bootstrap 3 collapse and transition plugins
Make sure to include *.JS files for plugins [collapse](http://getbootstrap.com/javascript/#collapse) and [transitions](http://getbootstrap.com/javascript/#transitions). They are included with [bootstrap in js/ directory](https://github.com/twbs/bootstrap/tree/master/js)
Alternatively you could include the whole bundle of bootstrap plugins from [bootstrap.js](https://github.com/twbs/bootstrap/tree/master/dist/js)

```html
<script type="text/javascript" src="/path/to/jquery.js"></script>
<script type="text/javascript" src="/path/to/moment.js"></script>
<script type="text/javascript" src="/path/to/bootstrap/js/transition.js"></script>
<script type="text/javascript" src="/path/to/bootstrap/js/collapse.js"></script>
<script type="text/javascript" src="/path/to/bootstrap/dist/bootstrap.min.js"></script>
<script type="text/javascript" src="/path/to/bootstrap-datetimepicker.min.js"></script>
```

## Knockout

```
ko.bindingHandlers.dateTimePicker = {
    init: function (element, valueAccessor, allBindingsAccessor) {
        //initialize datepicker with some optional options
        var options = allBindingsAccessor().dateTimePickerOptions || {};
        $(element).datetimepicker(options);

        //when a user changes the date, update the view model
        ko.utils.registerEventHandler(element, "dp.change", function (event) {
            var value = valueAccessor();
            if (ko.isObservable(value)) {
                if (event.date != null && !(event.date instanceof Date)) {
                    value(event.date.toDate());
                } else {
                    value(event.date);
                }
            }
        });

        ko.utils.domNodeDisposal.addDisposeCallback(element, function () {
            var picker = $(element).data("DateTimePicker");
            if (picker) {
                picker.destroy();
            }
        });
    },
    update: function (element, valueAccessor, allBindings, viewModel, bindingContext) {

        var picker = $(element).data("DateTimePicker");
        //when the view model is updated, update the widget
        if (picker) {
            var koDate = ko.utils.unwrapObservable(valueAccessor());

            //in case return from server datetime i am get in this form for example /Date(93989393)/ then fomat this
            koDate = (typeof (koDate) !== 'object') ? new Date(parseFloat(koDate.replace(/[^0-9]/g, ''))) : koDate;

            picker.date(koDate);
        }
    }
};
```

### CSS styles

#### Using LESS
```css
@import "/path/to/bootstrap/less/variables";
@import "/path/to/bootstrap-datetimepicker/src/less/bootstrap-datetimepicker-build.less";

// [...] your custom styles and variables
```

Using CSS (default color palette)
```html
<link rel="stylesheet" href="/path/to/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css" />
```