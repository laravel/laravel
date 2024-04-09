/**
 * Star Ratings (jquery)
 */

'use strict';
$(function () {
  var basicRatings = $('.basic-ratings'),
    customSvg = $('.custom-svg-ratings'),
    multiColor = $('.multi-color-ratings'),
    halfStar = $('.half-star-ratings'),
    fullStar = $('.full-star-ratings'),
    readOnlyRatings = $('.read-only-ratings'),
    onSetEvents = $('.onset-event-ratings'),
    onChangeEvents = $('.onChange-event-ratings'),
    ratingMethods = $('.methods-ratings'),
    initializeRatings = $('.btn-initialize'),
    destroyRatings = $('.btn-destroy'),
    getRatings = $('.btn-get-rating'),
    setRatings = $('.btn-set-rating');

  // Basic Ratings
  // --------------------------------------------------------------------
  if (basicRatings) {
    basicRatings.rateYo({
      rating: 3.6,
      rtl: isRtl
    });
  }

  // Custom SVG Ratings
  // --------------------------------------------------------------------
  if (customSvg) {
    customSvg.rateYo({
      rating: 3.2,
      starSvg:
        "<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'>" +
        "<path d='M12 6.76l1.379 4.246h4.465l-3.612 2.625 1.379" +
        ' 4.246-3.611-2.625-3.612 2.625' +
        ' 1.379-4.246-3.612-2.625h4.465l1.38-4.246zm0-6.472l-2.833' +
        ' 8.718h-9.167l7.416 5.389-2.833 8.718 7.417-5.388' +
        ' 7.416 5.388-2.833-8.718' +
        " 7.417-5.389h-9.167l-2.833-8.718z'-></path>",
      rtl: isRtl
    });
  }

  // Multi Color Ratings
  // --------------------------------------------------------------------
  if (multiColor) {
    multiColor.rateYo({
      rtl: isRtl,
      multiColor: {
        startColor: '#fffca0',
        endColor: '#fdac41'
      }
    });
  }

  // Half Star Ratings
  // --------------------------------------------------------------------
  if (halfStar) {
    halfStar.rateYo({
      rtl: isRtl,

      rating: 2
    });
  }

  // Full Star Ratings
  // --------------------------------------------------------------------
  if (fullStar) {
    fullStar.rateYo({
      rtl: isRtl,

      rating: 2
    });
  }

  // Read Only Ratings
  // --------------------------------------------------------------------
  if (readOnlyRatings) {
    readOnlyRatings.rateYo({
      rating: 2,
      rtl: isRtl
    });
  }

  // Ratings Events
  // --------------------------------------------------------------------

  // onSet Event
  if (onSetEvents) {
    onSetEvents
      .rateYo({
        rtl: isRtl
      })
      .on('rateyo.set', function (e, data) {
        alert('The rating is set to ' + data.rating + '!');
      });
  }

  // onChange Event
  if (onChangeEvents) {
    onChangeEvents
      .rateYo({
        rtl: isRtl
      })
      .on('rateyo.change', function (e, data) {
        var rating = data.rating;
        $(this).parent().find('.counter').text(rating);
      });
  }

  // Ratings Methods
  // --------------------------------------------------------------------
  if (ratingMethods) {
    var $instance = ratingMethods.rateYo({
      rtl: isRtl
    });

    if (initializeRatings) {
      initializeRatings.on('click', function () {
        $instance.rateYo({
          rtl: isRtl
        });
      });
    }

    if (destroyRatings) {
      destroyRatings.on('click', function () {
        if ($instance.hasClass('jq-ry-container')) {
          $instance.rateYo('destroy');
        } else {
          window.alert('Please Initialize Ratings First');
        }
      });
    }

    if (getRatings) {
      getRatings.on('click', function () {
        if ($instance.hasClass('jq-ry-container')) {
          var rating = $instance.rateYo('rating');
          window.alert('Current Ratings are ' + rating);
        } else {
          window.alert('Please Initialize Ratings First');
        }
      });
    }

    if (setRatings) {
      setRatings.on('click', function () {
        if ($instance.hasClass('jq-ry-container')) {
          $instance.rateYo('rating', 1);
        } else {
          window.alert('Please Initialize Ratings First');
        }
      });
    }
  }
});
