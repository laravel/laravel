"use strict";

var sparkline_values = [110, 147, 324, 108, 235, 498, 346, 525, 382, 214, 427, 424, 239, 236, 475, 569],
  sparkline_values_bar = [10, 7, 4, 8, 5, 8, 6, 5, 2, 4, 7, 4, 9, 10, 7, 4, 8, 5, 8, 6, 5, 4],
  sparkline_pie = [30, 20, 10];

$('.sparkline-inline').sparkline(sparkline_values, {
  type: 'line',
  width: '100%',
  height: '200',
  lineWidth: 3,
  lineColor: 'rgba(63,82,227,.1)',
  fillColor: 'rgba(63,82,227,.4)',
  highlightSpotColor: 'rgba(63,82,227,.1)',
  highlightLineColor: 'rgba(63,82,227,.1)',
  spotRadius: 3,
});

$('.sparkline-line').sparkline(sparkline_values, {
  type: 'line',
  width: '100%',
  height: '200',
  lineWidth: 3,
  lineColor: 'rgba(63,82,227,.6)',
  fillColor: 'transparent',
  highlightSpotColor: 'rgba(63,82,227,.1)',
  highlightLineColor: 'rgba(63,82,227,.1)',
  spotRadius: 3,
});

$(".sparkline-bar").sparkline(sparkline_values_bar, {
  type: 'bar',
  width: '100%',
  height: '200',
  barColor: 'rgb(63,82,227)',
  barWidth: 20
});

$(".sparkline-pie").sparkline(sparkline_pie, {
  type: 'pie',
  width: 'auto',
  height: '200',
  barWidth: 20
});