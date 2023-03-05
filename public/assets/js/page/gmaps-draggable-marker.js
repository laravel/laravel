"use strict";

var input_lat = $("#input-lat"), // latitude input text
  input_lng = $("#input-lng"), // longitude input text
  map = new GMaps({ // init map
    div: '#map',
    lat: 23.014711,
    lng: 72.530810
  });

// add marker
var marker = map.addMarker({
  lat: 23.014711,
  lng: 72.530810,
  draggable: true,
});

// when the map is clicked
map.addListener("click", function (e) {
  var lat = e.latLng.lat(),
    lng = e.latLng.lng();

  // move the marker position
  marker.setPosition({
    lat: lat,
    lng: lng
  });
  update_position();
});

// when the marker is dragged
marker.addListener('drag', function (e) {
  update_position();
});

// set the value to latitude and longitude input
update_position();
function update_position() {
  var lat = marker.getPosition().lat(), lng = marker.getPosition().lng();
  input_lat.val(lat);
  input_lng.val(lng);
}

// move the marker when the latitude and longitude inputs change in value
$("#input-lat,#input-lng").blur(function () {
  var lat = parseInt(input_lat.val()),
    lng = parseInt(input_lng.val());

  marker.setPosition({
    lat: lat,
    lng: lng
  });
  map.setCenter({
    lat: lat,
    lng: lng
  });
});
