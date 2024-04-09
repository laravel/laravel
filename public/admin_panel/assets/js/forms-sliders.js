/**
 * Sliders
 */
'use strict';

(function () {
  const sliderBasic = document.getElementById('slider-basic'),
    sliderHandles = document.getElementById('slider-handles'),
    sliderSteps = document.getElementById('slider-steps'),
    sliderTap = document.getElementById('slider-tap'),
    sliderDrag = document.getElementById('slider-drag'),
    sliderFixedDrag = document.getElementById('slider-fixed-drag'),
    sliderCombined = document.getElementById('slider-combined-options'),
    sliderHover = document.getElementById('slider-hover'),
    sliderPips = document.getElementById('slider-pips');

  // Basic
  // --------------------------------------------------------------------

  if (sliderBasic) {
    noUiSlider.create(sliderBasic, {
      start: [50],
      connect: [true, false],
      direction: isRtl ? 'rtl' : 'ltr',
      range: {
        min: 0,
        max: 100
      }
    });
  }

  // Handles
  // --------------------------------------------------------------------
  if (sliderHandles) {
    noUiSlider.create(sliderHandles, {
      start: [0, 50],
      direction: isRtl ? 'rtl' : 'ltr',
      step: 5,
      connect: true,
      range: {
        min: 0,
        max: 100
      },
      pips: {
        mode: 'range',
        density: 5,
        stepped: true
      }
    });
  }

  // Steps
  // --------------------------------------------------------------------
  if (sliderSteps) {
    noUiSlider.create(sliderSteps, {
      start: [0, 30],
      snap: true,
      connect: true,
      direction: isRtl ? 'rtl' : 'ltr',
      range: {
        min: 0,
        '10%': 10,
        '20%': 20,
        '30%': 30,
        '40%': 40,
        '50%': 50,
        max: 100
      }
    });
  }

  // Tap
  // --------------------------------------------------------------------
  if (sliderTap) {
    noUiSlider.create(sliderTap, {
      start: [10, 30],
      behaviour: 'tap',
      direction: isRtl ? 'rtl' : 'ltr',
      connect: true,
      range: {
        min: 10,
        max: 100
      }
    });
  }

  // Drag
  // --------------------------------------------------------------------
  if (sliderDrag) {
    noUiSlider.create(sliderDrag, {
      start: [40, 60],
      limit: 20,
      behaviour: 'drag',
      direction: isRtl ? 'rtl' : 'ltr',
      connect: true,
      range: {
        min: 20,
        max: 80
      }
    });
  }

  // Fixed Drag
  // --------------------------------------------------------------------
  if (sliderFixedDrag) {
    noUiSlider.create(sliderFixedDrag, {
      start: [40, 60],
      behaviour: 'drag-fixed',
      direction: isRtl ? 'rtl' : 'ltr',
      connect: true,
      range: {
        min: 20,
        max: 80
      }
    });
  }

  // Combined Options
  // --------------------------------------------------------------------
  if (sliderCombined) {
    noUiSlider.create(sliderCombined, {
      start: [40, 60],
      behaviour: 'drag-tap',
      direction: isRtl ? 'rtl' : 'ltr',
      connect: true,
      range: {
        min: 20,
        max: 80
      }
    });
  }

  // Hover
  // --------------------------------------------------------------------
  if (sliderHover) {
    noUiSlider.create(sliderHover, {
      start: 20,
      behaviour: 'hover-snap-tap',
      direction: isRtl ? 'rtl' : 'ltr',
      range: {
        min: 0,
        max: 100
      }
    });

    sliderHover.noUiSlider.on('hover', function (value) {
      document.getElementById('slider-val').innerHTML = value;
    });
  }

  // Scale and Pips
  // --------------------------------------------------------------------
  if (sliderPips) {
    noUiSlider.create(sliderPips, {
      start: [10],
      behaviour: 'tap-drag',
      step: 10,
      tooltips: true,
      range: {
        min: 0,
        max: 100
      },
      pips: {
        mode: 'steps',
        stepped: true,
        density: 5
      },
      direction: isRtl ? 'rtl' : 'ltr'
    });
  }

  // colors
  // --------------------------------------------------------------------
  const sliderPrimary = document.getElementById('slider-primary'),
    sliderSuccess = document.getElementById('slider-success'),
    sliderDanger = document.getElementById('slider-danger'),
    sliderInfo = document.getElementById('slider-info'),
    sliderWarning = document.getElementById('slider-warning'),
    colorOptions = {
      start: [30, 50],
      connect: true,
      behaviour: 'tap-drag',
      step: 10,
      tooltips: true,
      range: {
        min: 0,
        max: 100
      },
      pips: {
        mode: 'steps',
        stepped: true,
        density: 5
      },
      direction: isRtl ? 'rtl' : 'ltr'
    };

  if (sliderPrimary) {
    noUiSlider.create(sliderPrimary, colorOptions);
  }
  if (sliderSuccess) {
    noUiSlider.create(sliderSuccess, colorOptions);
  }
  if (sliderDanger) {
    noUiSlider.create(sliderDanger, colorOptions);
  }
  if (sliderInfo) {
    noUiSlider.create(sliderInfo, colorOptions);
  }
  if (sliderWarning) {
    noUiSlider.create(sliderWarning, colorOptions);
  }

  // Dynamic Slider
  // --------------------------------------------------------------------
  const dynamicSlider = document.getElementById('slider-dynamic'),
    sliderSelect = document.getElementById('slider-select'),
    sliderInput = document.getElementById('slider-input');

  if (dynamicSlider) {
    noUiSlider.create(dynamicSlider, {
      start: [10, 30],
      connect: true,
      direction: isRtl ? 'rtl' : 'ltr',
      range: {
        min: -20,
        max: 40
      }
    });

    dynamicSlider.noUiSlider.on('update', function (values, handle) {
      const value = values[handle];

      if (handle) {
        sliderInput.value = value;
      } else {
        sliderSelect.value = Math.round(value);
      }
    });
  }

  if (sliderSelect) {
    for (let i = -20; i <= 40; i++) {
      let option = document.createElement('option');
      option.text = i;
      option.value = i;

      sliderSelect.appendChild(option);
    }
    sliderSelect.addEventListener('change', function () {
      dynamicSlider.noUiSlider.set([this.value, null]);
    });
  }

  if (sliderInput) {
    sliderInput.addEventListener('change', function () {
      dynamicSlider.noUiSlider.set([null, this.value]);
    });
  }

  // Vertical
  // --------------------------------------------------------------------
  const defaultVertical = document.getElementById('slider-vertical'),
    connectVertical = document.getElementById('slider-connect-upper'),
    tooltipVertical = document.getElementById('slider-vertical-tooltip'),
    limitVertical = document.getElementById('slider-vertical-limit');

  // Default
  if (defaultVertical) {
    defaultVertical.style.height = '200px';
    noUiSlider.create(defaultVertical, {
      start: [40, 60],
      orientation: 'vertical',
      behaviour: 'drag',
      connect: true,
      range: {
        min: 0,
        max: 100
      }
    });
  }

  // Connect Upper
  if (connectVertical) {
    connectVertical.style.height = '200px';
    noUiSlider.create(connectVertical, {
      start: 40,
      orientation: 'vertical',
      behaviour: 'drag',
      connect: 'upper',
      range: {
        min: 0,
        max: 100
      }
    });
  }

  // Vertical Tooltip
  if (tooltipVertical) {
    tooltipVertical.style.height = '200px';
    noUiSlider.create(tooltipVertical, {
      start: 10,
      orientation: 'vertical',
      behaviour: 'drag',
      tooltips: true,
      range: {
        min: 0,
        max: 100
      }
    });
  }

  // Limit
  if (limitVertical) {
    limitVertical.style.height = '200px';
    noUiSlider.create(limitVertical, {
      start: [0, 40],
      orientation: 'vertical',
      behaviour: 'drag',
      limit: 60,
      tooltips: true,
      connect: true,
      range: {
        min: 0,
        max: 100
      }
    });
  }
})();
