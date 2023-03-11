"use strict";

$('#visitorMap').vectorMap({
  map: 'world_en',
  backgroundColor: '#ffffff',
  borderColor: '#f2f2f2',
  borderOpacity: .8,
  borderWidth: 1,
  hoverColor: '#000',
  hoverOpacity: .8,
  color: '#ddd',
  normalizeFunction: 'linear',
  selectedRegions: false,
  showTooltip: true,
  pins: {
    id: '<div class="jqvmap-circle"></div>',
    my: '<div class="jqvmap-circle"></div>',
    th: '<div class="jqvmap-circle"></div>',
    sy: '<div class="jqvmap-circle"></div>',
    eg: '<div class="jqvmap-circle"></div>',
    ae: '<div class="jqvmap-circle"></div>',
    nz: '<div class="jqvmap-circle"></div>',
    tl: '<div class="jqvmap-circle"></div>',
    ng: '<div class="jqvmap-circle"></div>',
    si: '<div class="jqvmap-circle"></div>',
    pa: '<div class="jqvmap-circle"></div>',
    au: '<div class="jqvmap-circle"></div>',
    ca: '<div class="jqvmap-circle"></div>',
    tr: '<div class="jqvmap-circle"></div>',
  },
  onRegionClick: function (element, code, region) {
    var opts = {
      title: 'Choosed',
      message: 'You clicked "'
        + region
        + '" which has the code: '
        + code.toUpperCase()
    };

    iziToast.info(opts);
  }
});
$('#visitorMap2').vectorMap({
  map: 'world_en',
  backgroundColor: '#ffffff',
  borderColor: '#f2f2f2',
  borderOpacity: .8,
  borderWidth: 1,
  hoverColor: '#000',
  hoverOpacity: .8,
  color: '#ddd',
  normalizeFunction: 'linear',
  selectedRegions: false,
  showTooltip: true,
  onRegionClick: function (element, code, region) {
    var message = 'You clicked "'
      + region
      + '" which has the code: '
      + code.toUpperCase();

    $("#flag-icon").removeClass(function (index, className) {
      return (className.match(/(^|\s)flag-icon-\S+/g) || []).join(' ');
    });
    $("#flag-icon").addClass('flag-icon-' + code);
  }
});
$('#visitorMap3').vectorMap({
  map: 'indonesia_id',
  backgroundColor: '#ffffff',
  borderColor: '#f2f2f2',
  borderOpacity: .8,
  borderWidth: 1,
  hoverColor: '#000',
  hoverOpacity: .8,
  color: '#ddd',
  normalizeFunction: 'linear',
  selectedRegions: false,
  showTooltip: true,
});
