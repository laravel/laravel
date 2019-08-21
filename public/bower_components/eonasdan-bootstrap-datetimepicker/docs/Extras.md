# Extras

Guides for making the picker work better with rails, IE, etc

## Rails 3

by [dhulihan](https://github.com/dhulihan)

You can easily override the default rails form helpers (`date_select` and `datetime_select`) with bootstrap-datetimepicker for a much nicer experience. 

```rb
# Add to config/initializers/form.rb or the end of app/helpers/application_helper.rb
module ActionView
  module Helpers
    class FormBuilder 
      def date_select(method, options = {}, html_options = {})
        existing_date = @object.send(method) 
        formatted_date = existing_date.to_date.strftime("%F") if existing_date.present?
        @template.content_tag(:div, :class => "input-group") do    
          text_field(method, :value => formatted_date, :class => "form-control datepicker", :"data-date-format" => "YYYY-MM-DD") +
          @template.content_tag(:span, @template.content_tag(:span, "", :class => "glyphicon glyphicon-calendar") ,:class => "input-group-addon")
        end
      end

      def datetime_select(method, options = {}, html_options = {})
        existing_time = @object.send(method) 
        formatted_time = existing_time.to_time.strftime("%F %I:%M %p") if existing_time.present?
        @template.content_tag(:div, :class => "input-group") do    
          text_field(method, :value => formatted_time, :class => "form-control datetimepicker", :"data-date-format" => "YYYY-MM-DD hh:mm A") +
          @template.content_tag(:span, @template.content_tag(:span, "", :class => "glyphicon glyphicon-calendar") ,:class => "input-group-addon")
        end
      end
    end
  end
end
```

The time format used here is ActiveRecord-friendly, which means it will be parsed correctly when passed in through `params` to your record.

That's all there is to it! Now all of your forms that use `datetime_select` or `date_select` will be automatically updated:

```erb
<% form_for @post do |f| %>
	<div class="form-group">
		<label>Published At</label>
		<%= f.datetime_select :published_at %>
	</div>
<% end %>
```

## IE 7

by [EquilibriumCST](https://github.com/EquilibriumCST)

I succeed to run this widget under IE7.
Here is what I did.

1. gliphicons are not working under IE7 so add [this css file](https://github.com/coliff/bootstrap-ie7). And this enables the icons.

2. Z-index problem with IE 7. I added position: relative and `z-index: 10` to the parent container. Otherwise popup is shown under the next elements.

3. JS events were not working well. 

If you open the datetimepicker widget and click on some button or date inside it, widget is automatically closed.
So I added `debug: true` as an option when initializing the widget. Why I did this? I saw on line 1121 from bootsrap-datetimepicker.js the code `'blur': options.debug ? '' : hide`. 
And now widget window is not closed on every click inside it, but now you can't close it anyway :) 
And closing should be done manually. I've added this document click handler. If you click something outside the widget, now closing works.

```
$(document).click(function(e){
			var target = $(e.target);
			if(target.parents('.bootstrap-datetimepicker-widget').length < 1 && !target.hasClass('datetimepickerInput') && !target.hasClass('datepickerIcon') && !target.hasClass('clockpickerIcon')){
				if($('.bootstrap-datetimepicker-widget').length > 0){
					$('#startDate').data('DateTimePicker').hide();
					$('#startTime').data('DateTimePicker').hide();
					$('.datetimepickerInput').blur();
				}
			}
		});
```


But if you have more than one widget on the page like I did, clicking on one widget does'n close the other. Added below lines and now all works fine.

```
$('#widget1').on("dp.show",function (e) {
	$('#widget2).data('DateTimePicker').hide();
});

$('#widget2').on("dp.show",function (e) {
	$('#widget1).data('DateTimePicker').hide();
});
```

I hope this will help to the others who are fighting with the old IE versions :)