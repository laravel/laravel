/*
 * MoonCake v1.3.1 - UI Components Demo JS
 *
 * This file is part of MoonCake, an Admin template build for sale at ThemeForest.
 * For questions, suggestions or support request, please mail me at maimairel@yahoo.com
 *
 * Development Started:
 * July 28, 2012
 * Last Update:
 * December 07, 2012
 *
 */

;(function( $, window, document, undefined ) {
			
	var demos = {
		basicDatepicker: function(target) {
			
			target.datepicker();
			
		}, 
		
		inlineDatepicker: function( target ) {
			
			target.datepicker( { showOtherMonths: true, onSelect: function( dateText, ins ) {
				$( '#dp-02-target' ).text( dateText );
			} } );
			
			$( '#dp-02-target' ).text( $.datepicker.formatDate( 'mm/dd/yy', target.datepicker( 'getDate' ) ) );
			
		}, 
		
		weekDatepicker: function( target ) {
			
			target.datepicker( { showOtherMonths: true, showWeek: true } );
			
		}, 
		
		changeYearMonth: function( target ) {
			
			target.datepicker( { changeMonth: true, changeYear: true } );
			
		}, 
		
		dateRangePicker: function( from, to ) {
			
			from.datepicker({
				defaultDate: "+1w",
				numberOfMonths: 3, 
				showOtherMonths: true, 
				onSelect: function( selectedDate ) {
					to.datepicker( "option", "minDate", selectedDate );
				}
			});
			
			to.datepicker({
				defaultDate: "+1w",
				numberOfMonths: 3, 
				showOtherMonths: true, 
				onSelect: function( selectedDate ) {
					from.datepicker( "option", "maxDate", selectedDate );
				}
			});
			
		}, 

		basicTimepicker: function( target ) {

			target.timepicker({});

		}, 

		dateTimepicker: function( target ) {

			target.datetimepicker({ ampm: true });

		}, 

		timepickerMili: function( target ) {

			target.timepicker({
				showSecond: true, 
				showMillisec: true, 
				timeFormat: 'hh:mm:ss:l'
			});

		}, 

		zebraBasic: function( target ) {

			target.Zebra_DatePicker();

		}, 

		zebraWeek: function( target ) {

			target.Zebra_DatePicker({
				show_week_number: 'Wk'
			});

		}, 

		zebraMonth: function( target ) {

			target.Zebra_DatePicker({
				format: 'm Y'
			});

		}, 

		basicSlider: function(target) {
			
			if($.fn.slider)
				target.slider({
					tooltip: 'none'
				});
			
		}, 
		
		basicRangeSlider: function(target) {
			if($.fn.slider)
				target.slider({
					range: true, 
					values: [100, 1500], 
					min: 0, 
					max: 2000, 
					orientation: 'horizontal', 
					tooltip: 'none'
				});
		}, 
		
		tooltipSlider: function(target) {
			
			if($.fn.slider)
				target.slider({ value: 36, range: 'min' });
			
		}, 
		
		tooltipRangeSlider: function(target) {
			if($.fn.slider)
				target.slider({
					range: true, 
					values: [250, 1455], 
					min: 0, 
					max: 2000, 
					orientation: 'horizontal'
				});
		}, 
		
		ticksSlider: function(target) {
			if($.fn.slider)
				target.slider({
					range: "min", 
					min: 0, 
					max: 2000, 
					value: 1238, 
					orientation: 'horizontal', 
					ticks: [0, '|', 500, '|', 1000, '|', 1500, '|', 2000]
				});
		}, 
		
		ticksRangeSlider: function(target) {
			if($.fn.slider)
				target.slider({
					range: true, 
					values: [250, 1455], 
					min: 0, 
					max: 2000, 
					orientation: 'horizontal', 
					ticks: [0, '|', 500, '|', 1000, '|', 1500, '|', 2000]
				});
		}, 
		
		snapToTicks: function(target) {
			if($.fn.slider)
				target.slider({
					value: 130, 
					min: 0, 
					max: 200, 
					step: 25, 
					range: "min", 
					orientation: 'horizontal', 
					ticks: [0, '|', 50, '|', 100, '|', 150, '|', 200]
				});
		}, 
		
		verticalSlider: function(target) {
			if($.fn.slider) {
				
				$( target ).children( "span" ).each(function() {
					// read initial values from markup and remove that
					var value = parseInt( $( this ).text(), 10 );
					$( this ).empty().slider({
						value: value,
						range: "min", 
						animate: true, 
						ticks: [0, '|', 50, '|',  100], 
						orientation: "vertical"
					});
				});
				
			}
		}, 

		basicPb: function( target ) {
			if( $.fn.progressbar ) {
				
				target.progressbar({
					'value': (Math.floor( Math.random() * 25 ) + 50), 
					'active': !!parseInt(Math.round(Math.random()), 10), 
					'striped': !!parseInt(Math.round(Math.random()), 10), 
					'showValue': !!parseInt(Math.round(Math.random()), 10), 
					'type': (['info', 'success', 'warning', 'danger'])[Math.floor(Math.random() * 4)]
				});
			}
		}, 

		basicDialog: function( target, trigger ) {
			target.dialog({
				autoOpen: false
			});

			trigger.on('click', function(e) {
				target.dialog( 'open' );
				e.preventDefault();
			});
		}, 

		modalDialog: function( target, trigger ) {
			target.dialog({
				autoOpen: false, 
				modal: true
			});

			trigger.on('click', function(e) {
				target.dialog( 'open' );
				e.preventDefault();
			});
		}
	};

	$(document).ready(function() {

		if( $.fn.progressbar ) {
		
			demos.basicPb( $('#demo-pb-01') );

			$( '#jui-pb-rand' ).on( 'click', function( e ) {
				$( '#demo-pb-01' ).progressbar( 'value', Math.floor(Math.random() * 80) + 20 );

				$( '#demo-pb-01' ).progressbar( 'option', 'active', !!parseInt(Math.round(Math.random()), 10) );
				$( '#demo-pb-01' ).progressbar( 'option', 'striped', !!parseInt(Math.round(Math.random()), 10) );
				$( '#demo-pb-01' ).progressbar( 'option', 'showValue', !!parseInt(Math.round(Math.random()), 10) );
				$( '#demo-pb-01' ).progressbar( 'option', 'type', (['info', 'success', 'warning', 'danger'])[Math.floor(Math.random() * 4)] );
			});
		}

		if( $.fn.slider ) {

			demos.basicSlider($('#demo-slider-01'));
			demos.basicRangeSlider($('#demo-slider-02'));
			demos.tooltipSlider($('#demo-slider-03'));
			demos.tooltipRangeSlider($('#demo-slider-04'));
			demos.ticksSlider($('#demo-slider-05'));
			demos.ticksRangeSlider($('#demo-slider-06'));
			demos.snapToTicks($('#demo-slider-07'));
			demos.verticalSlider($('#demo-slider-08'));
		}
		
		if( $.fn.datepicker ) {
			
			demos.basicDatepicker( $( '.datepicker-basic' ) );
			demos.inlineDatepicker( $( '.datepicker-inline' ) );
			demos.weekDatepicker( $( '.datepicker-week' ) );
			demos.changeYearMonth( $( '.datepicker-cmy' ) );
			demos.dateRangePicker( $( '#datepicker-from' ), $( '#datepicker-to' ) );
			
		}

		if( $.fn.timepicker ) {

			demos.basicTimepicker( $( '#timepicker-basic' ) );
			demos.timepickerMili( $( '#timepicker-mili' ) );
			demos.dateTimepicker( $( '#timepicker-date' ) ) ;
		}

		if( $.fn.Zebra_DatePicker ) {

			demos.zebraBasic( $('#zebradp-basic') );
			demos.zebraWeek( $('#zebradp-week') );
			demos.zebraMonth( $('#zebradp-month') );
		}
		
		if( $.fn.miniColors ) {
			
			$( '.minicolors' ).miniColors();
			$( '.minicolors-opacity' ).miniColors({ opacity: true });			
		}
		
		if( $.fn.farbtastic ) {
			$( '.farbtastic' ).farbtastic( function( color ) { $( '#farbtastic-demo1' ).css( 'color', color ).text( color ); } );
		}

		if( $.fn.dialog ) {
			demos.basicDialog( $('#dialog-default'), $('#basic-dialog') );
			demos.modalDialog( $('#dialog-modal'), $('#modal-dialog') );
		}

	});
	
}) (jQuery, window, document);