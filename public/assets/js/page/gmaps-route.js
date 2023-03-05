"use strict";

// initialize map
var map = new GMaps({
  div: '#map',
  lat: 23.014711,
  lng: 72.530810,
  zoom: 8
});

// draw route between 'origin' to 'destination'
map.drawRoute({
  origin: [23.014711, 72.530810],
  destination: [22.291330, 70.802255],
  travelMode: 'driving',
  strokeColor: '#131540',
  strokeOpacity: 0.6,
  strokeWeight: 6
});