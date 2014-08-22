/*
 * MoonCake v1.3.1 - Charts Demo JS
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
		
		lineCharts: function( target ) {

			var 
			
			d1 = [4.3, 5.1, 4.3, 5.2, 5.4, 4.7, 3.5, 4.1, 5.6, 7.4, 6.9, 7.1,
				7.9, 7.9, 7.5, 6.7, 7.7, 7.7, 7.4, 7.0, 7.1, 5.8, 5.9, 7.4,
				8.2, 8.5, 9.4, 8.1, 10.9, 10.4, 10.9, 12.4, 12.1, 9.5, 7.5,
				7.1, 7.5, 8.1, 6.8, 3.4, 2.1, 1.9, 2.8, 2.9, 1.3, 4.4, 4.2,
				3.0, 3.0], 
			
			d2 = [0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.1, 0.0, 0.3, 0.0,
				0.0, 0.4, 0.0, 0.1, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0,
				0.0, 0.6, 1.2, 1.7, 0.7, 2.9, 4.1, 2.6, 3.7, 3.9, 1.7, 2.3,
				3.0, 3.3, 4.8, 5.0, 4.8, 5.0, 3.2, 2.0, 0.9, 0.4, 0.3, 0.5, 0.4], 
			
			options = {
				series: {
					lines: { 
						show: true, 
						fill: true, 
						lineWidth: 4, 
						steps: false, 
						fillColor: { colors: [{opacity: 0.25}, {opacity: 0}] } 
					},
					points: { 
						show: true, 
						radius: 4, 
						fill: true
					}
				}, 
				tooltip: true, 
				tooltipOpts: {
					content: '%s: %y'
				}, 
				xaxis: { mode: "time" }, 
				grid: { borderWidth: 0, hoverable: true }
			};
			
			var dt1 = [], dt2 = [], st = new Date(2009, 9, 6).getTime();
			
			for( var i = 0; i < d2.length; i++ )
			{
				dt1.push([st + i * 3600000, parseFloat( (d1[i]).toFixed( 3 ) )]);
				dt2.push([st + i * 3600000, parseFloat( (d2[i]).toFixed( 3 ) )]);
			}
			
			var data = [
				{ data: dt1, color: '#a856a1', label: 'Humidity' }, 
				{ data: dt2, color: '#f36a30', label: 'Temperature in C<sup>o</sup>', points: { show: false }, lines: { lineWidth: 2, fill: false } }
			];
			
			$.plot( target, data, options );
			
		}, 
		
		pieChart: function( target ) {
			
			var data = [
				{ label: "Hotel Mulia Senayan", data: Math.random() * 2500 + 500, color: "#115b74" }, 
				{ label: "Hotel Grand Hyatt", data: Math.random() * 2500 + 500, color: "#e49600" }, 
				{ label: "Shangrila Hotel", data: Math.random() * 2500 + 500, color: "#cc2b36" }, 
				{ label: "Twin Plaza Hotel", data: Math.random() * 2500 + 500, color: "#f1c175" }, 
				{ label: "Merlynn Park Hotel", data: Math.random() * 2500 + 500, color: "#95204e" }, 
				{ label: "Hotel Ciputra", data: Math.random() * 2500 + 500, color: "#e18876" }, 
				{ label: "Aston Marina Hotel", data: Math.random() * 2500 + 500, color: "#7d2880" }, 
				{ label: "Four Seasons Hotel Jakarta", data: Math.random() * 2500 + 500, color: "#222120" }, 
				{ label: "Putri Duyung Ancol Hotel", data: Math.random() * 2500 + 500, color: "#c6a5ca" }, 
				{ label: "Hotel Arcadia Jakarta", data: Math.random() * 2500 + 500, color: "#e8a6b1" }
			], 
			id = target.parents('.tab-pane').attr('id');
			
			var opts = {
				series: {
					pie: {
						show: true,  
						innerRadius: 0.4, 
						offset: {
							left: 0
						}, 
						stroke: {
							width: 4
						}
					}
				}, 
				legend: {
					position: 'sw'
				}, 
				grid: {
					hoverable: true
				}
			};
			
			$.plot(target, data, opts);
		}, 
		
		barChart: function( target, hor ) {

				//Display horizontal graph
				var d1 = [];
				for (var i = 0; i <= 4; i += 1) {
					if(!hor)
						d1.push([i, parseInt(Math.random() * 80) + 20]);
					else
						d1.push([parseInt(Math.random() * 80) + 20, i]);
				}
			
				var d2 = [];
				for (var i = 0; i <= 4; i += 1) {
					if(!hor)
						d2.push([i, parseInt(Math.random() * 80) + 20]);
					else
						d2.push([parseInt(Math.random() * 80) + 20, i]);
				}
			
				var d3 = [];
				for (var i = 0; i <= 4; i += 1) {
					if(!hor)
						d3.push([i, parseInt(Math.random() * 80) + 20]);
					else
						d3.push([parseInt(Math.random() * 80) + 20, i]);
				}
							
				var data = [];
				data.push({
					data: d1, 
					label: 'Cars', 
					bars: {
						barWidth: 0.15, 
						order: 1, 
						fillColor: '#115b74'
					}
				});
				data.push({
					data: d2, 
					label: 'Helicopters', 
					bars: {
						barWidth: 0.15, 
						order: 2, 
						fillColor: '#e49600'
					}
				});
				data.push({
					data: d3, 
					label: 'Scooters', 
					bars: {
						barWidth: 0.15, 
						order: 3, 
						fillColor: '#86c5da'
					}
				});
				
				var options = {
					grid: {
						hoverable: true, 
						borderWidth: 0
					}, 
					bars: {
						horizontal: hor, 
						show: true, 
						align: 'center', 
						lineWidth: 0
					}, 
					legend: {
						show: false
					}, 
					tooltip: true
				};
			
			$.plot( target, data, options );
			
		}, 

		lineChartsWithB: function( target ) {
			var goals = [],
				actuals = [];
			
			for( var i = 0; i < 10; i++ ) {
				var goal = Math.floor( 2400 + Math.random() * 600 ),	
					t = new Date(2011, i, 01).getTime() + (24 * 60 * 60 * 1000);
				
				goal = Math.ceil(goal / 10) * 10;
				goals.push([t, goal]);
				actuals.push([t, Math.floor(goal - (i * 15) + Math.random() * (i * 30))]);
			}
			
			var data = [
				{
					data: goals, 
					label: "Target Revenue", 
					color: '#08c', 
					bars: { 
						show: true, 
						barWidth: 10 * 24 * 60 * 60 * 1000, 
						align: "center"
					}
				}, {
					data: actuals, 
					label: "Actual Revenue", 
					color: '#CC2B36', 
					lines: {
						show: true 
					}, 
					points: {
						show: true, 
						radius: 4
					}
				}
			], 
			options = {
				xaxis: {
					mode: 'time'
				}, 
				tooltip: true, 
				tooltipOpts: {
					content: '%x - %y', 
					dateFormat: '%b %y'
				}, 
				grid: {
					borderWidth: 0, 
					hoverable: true 
				}
			};

			$.plot(target, data, options);
		}, 

		updating: function( target ) {
			// we use an inline data source in the example, usually data would
			// be fetched from a server
			var data = [], totalPoints = 200;

			function getRandomData() {
				if (data.length > 0)
					data = data.slice(1);

				// do a random walk
				while (data.length < totalPoints) {
					var prev = data.length > 0 ? data[data.length - 1] : 50;
					var y = prev + Math.random() * 10 - 5;
					if (y < 0)
					y = 0;
					if (y > 100)
					y = 100;
					data.push(y);
				}

				// zip the generated y values with the x values
				var res = [];
				for (var i = 0; i < data.length; ++i)
				res.push([i, data[i]])
				return res;
			}

			// setup plot
			var options = {
				yaxis: { min: 0, max: 100 },
				xaxis: { min: 0, max: 100 },
				colors: ["#26b"],
				series: {
					lines: { 
						lineWidth: 2, 
						fill: true,
						fillColor: { colors: [ { opacity: 0.4 }, { opacity: 0 } ] },
						steps: false
					}
				}, 
				grid: {
					borderWidth: 0
				}
			};

			var plot = $.plot(target, [ getRandomData() ], options);

			function update() {
				plot.setData([ getRandomData() ]);
				// since the axes don't change, we don't need to call plot.setupGrid()
				plot.draw();

				setTimeout(update, 500);
			}

			update();
		}
		
	};
	
	$(window).load(function() { });
	
	$(document).ready(function() {	
		
		if($.plot) {

			demos.lineCharts( $('#demo-charts-01') );

			demos.lineChartsWithB( $('#demo-charts-02') );
			
			demos.barChart( $('#demo-charts-03'), false );

			demos.barChart( $('#demo-charts-04'), true );

			demos.pieChart( $('#demo-charts-05') );

			demos.updating( $('#demo-charts-06') );
		}
	});
	
}) (jQuery, window, document);