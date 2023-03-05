"use strict";

// initialize map
var map = new GMaps({
  div: '#map',
  lat: 23.014711,
  lng: 72.530810
});

// when the form is submitted
$("#search-form").submit(function (e) {
  e.preventDefault();

  // initialize map geocode
  GMaps.geocode({
    address: $('#address').val(),
    callback: function (results, status) {
      if (status == 'OK') {
        var latlng = results[0].geometry.location;
        map.setCenter(latlng.lat(), latlng.lng());
        map.addMarker({
          lat: latlng.lat(),
          lng: latlng.lng()
        });
      }
    }
  });
})