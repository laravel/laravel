/*
 * MoonCake v1.3.1 - Calendar Demo JS
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
			
	var date = new Date();
	var d = date.getDate();
	var m = date.getMonth();
	var y = date.getFullYear();
	
	var demos = {
		eventExamples: {
			theEvents: [
				{
					title: 'All Day Event',
					start: new Date(y, m, 1)
				},
				{
					title: 'Long Event',
					start: new Date(y, m, d-5),
					end: new Date(y, m, d-2)
				},
				{
					id: 999,
					title: 'Repeating Event',
					start: new Date(y, m, d-3, 16, 0),
					allDay: false
				},
				{
					id: 999,
					title: 'Repeating Event',
					start: new Date(y, m, d+4, 16, 0),
					allDay: false
				},
				{
					title: 'Meeting',
					start: new Date(y, m, d, 10, 30),
					allDay: false
				},
				{
					title: 'Lunch',
					start: new Date(y, m, d, 12, 0),
					end: new Date(y, m, d, 14, 0),
					allDay: false
				},
				{
					title: 'Birthday Party',
					start: new Date(y, m, d+1, 19, 0),
					end: new Date(y, m, d+1, 22, 30),
					allDay: false
				},
				{
					title: 'Click for Google',
					start: new Date(y, m, 28),
					end: new Date(y, m, 29),
					url: 'http://google.com/'
				}
			]
		}, 
		
		buttonShowOff: function(target) {
			target.fullCalendar({
				header: {
					left: 'prev next prevYear nextYear today',
					center: 'title',
					right: 'month agendaWeek agendaDay'
				}, 
				editable: true, 
				events: this.eventExamples.theEvents, 
				
				buttonText: {
					prev: '<i class="icon-caret-left"></i>', 
					next: '<i class="icon-caret-right"></i>', 
					prevYear: '<i class="icon-caret-left"></i><i class="icon-caret-left"></i>', 
					nextYear: '<i class="icon-caret-right"></i><i class="icon-caret-right"></i>'
				}
			});
		}, 
		
		events: function(target) {
			target.fullCalendar({
				events: 'http://www.google.com/calendar/feeds/usa__en%40holiday.calendar.google.com/public/basic', 
				
				header: {
					left: 'prev today', 
					center: 'title', 
					right: 'next'
				}, 
				
				buttonText: {
					prev: '<i class="icon-caret-left"></i>', 
					next: '<i class="icon-caret-right"></i>'
				}
			});
		}
	};

	$(document).ready(function() { });
	
	$(window).load(function() {
		
		// When all page resources has finished loading
		
		if($.fn.fullCalendar) {
			
			demos.buttonShowOff($('#demo-calendar-01'));
			demos.events($('#demo-calendar-02'));
			
		}
	});
	
}) (jQuery, window, document);