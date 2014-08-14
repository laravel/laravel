/* Tamil (UTF-8) initialisation for the jQuery UI date picker plugin. */
/* Written by S A Sureshkumar (saskumar@live.com). */
jQuery(function($){
	$.datepicker.regional['ta'] = {
		closeText: 'மூடு',
		prevText: 'முன்னையது',
		nextText: 'அடுத்தது',
		currentText: 'இன்று',
		monthNames: ['தை','மாசி','பங்குனி','சித்திரை','வைகாசி','ஆனி',
		'ஆடி','ஆவணி','புரட்டாசி','ஐப்பசி','கார்த்திகை','மார்கழி'],
		monthNamesShort: ['தை','மாசி','பங்','சித்','வைகா','ஆனி',
		'ஆடி','ஆவ','புர','ஐப்','கார்','மார்'],
		dayNames: ['ஞாயிற்றுக்கிழமை','திங்கட்கிழமை','செவ்வாய்க்கிழமை','புதன்கிழமை','வியாழக்கிழமை','வெள்ளிக்கிழமை','சனிக்கிழமை'],
		dayNamesShort: ['ஞாயிறு','திங்கள்','செவ்வாய்','புதன்','வியாழன்','வெள்ளி','சனி'],
		dayNamesMin: ['ஞா','தி','செ','பு','வி','வெ','ச'],
		weekHeader: 'Не',
		dateFormat: 'dd/mm/yy',
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''};
	$.datepicker.setDefaults($.datepicker.regional['ta']);
});
