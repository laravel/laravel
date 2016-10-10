jQuery(function($) {
    var themap = $('<div id="themap"></div>').css({
        'width': '90%',
        'height': '500px'
    }).insertBefore('ul.people');

    var mapstraction = new Mapstraction('themap','google');
    mapstraction.addControls({
        zoom: 'large',
        map_type: true
    });

    mapstraction.setCenterAndZoom(
        new LatLonPoint(30, -0.126236),
        2 // Zoom level appropriate for Brighton city centre
    );

    $('.vcard').each(function() {
        var hcard = $(this);
    
        var latitude = hcard.find('.geo .latitude').text();
        var longitude = hcard.find('.geo .longitude').text();
    
        var marker = new Marker(new LatLonPoint(latitude, longitude));
        marker.setInfoBubble(
            '<div class="bubble">' + hcard.html() + '</div>'
        );
        mapstraction.addMarker(marker);
    });
});