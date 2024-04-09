/**
 * UI Carousel
 */

'use strict';

(function () {
  const swiperDefault = document.querySelector('#swiper-default'),
    swiperWithArrows = document.querySelector('#swiper-with-arrows'),
    swiperWithPagination = document.querySelector('#swiper-with-pagination'),
    swiperWithProgress = document.querySelector('#swiper-with-progress'),
    swiperWithScrollbar = document.querySelector('#swiper-with-scrollbar'),
    verticalSwiper = document.querySelector('#swiper-vertical'),
    swiperMultipleSlides = document.querySelector('#swiper-multiple-slides'),
    swiper3dCoverflowEffect = document.querySelector('#swiper-3d-coverflow-effect'),
    swiper3dCubeEffect = document.querySelector('#swiper-3d-cube-effect'),
    swiper3dFlipEffect = document.querySelector('#swiper-3d-flip-effect'),
    galleryThumbs = document.querySelector('.gallery-thumbs'),
    galleryTop = document.querySelector('.gallery-top');
  let galleryInstance;

  // Default
  // --------------------------------------------------------------------
  if (swiperDefault) {
    new Swiper(swiperDefault, {
      slidesPerView: 'auto'
    });
  }

  // With arrows
  // --------------------------------------------------------------------
  if (swiperWithArrows) {
    new Swiper(swiperWithArrows, {
      slidesPerView: 'auto',
      navigation: {
        prevEl: '.swiper-button-prev',
        nextEl: '.swiper-button-next'
      }
    });
  }

  // With pagination
  // --------------------------------------------------------------------
  if (swiperWithPagination) {
    new Swiper(swiperWithPagination, {
      slidesPerView: 'auto',
      pagination: {
        clickable: true,
        el: '.swiper-pagination'
      }
    });
  }

  // With progress
  // --------------------------------------------------------------------
  if (swiperWithProgress) {
    new Swiper(swiperWithProgress, {
      slidesPerView: 'auto',
      pagination: {
        type: 'progressbar',
        el: '.swiper-pagination'
      },
      navigation: {
        prevEl: '.swiper-button-prev',
        nextEl: '.swiper-button-next'
      }
    });
  }

  // With scrollbar
  // --------------------------------------------------------------------
  if (swiperWithScrollbar) {
    new Swiper(swiperWithScrollbar, {
      scrollbar: {
        hide: true,
        el: '.swiper-scrollbar'
      }
    });
  }

  // Vertical
  // --------------------------------------------------------------------
  if (verticalSwiper) {
    new Swiper(verticalSwiper, {
      direction: 'vertical',
      pagination: {
        clickable: true,
        el: '.swiper-pagination'
      }
    });
  }

  // Multiple slides
  // --------------------------------------------------------------------
  if (swiperMultipleSlides) {
    new Swiper(swiperMultipleSlides, {
      slidesPerView: 3,
      spaceBetween: 30,
      pagination: {
        clickable: true,
        el: '.swiper-pagination'
      }
    });
  }

  // 3D coverflow effect
  // --------------------------------------------------------------------
  if (swiper3dCoverflowEffect) {
    new Swiper(swiper3dCoverflowEffect, {
      effect: 'coverflow',
      grabCursor: true,
      centeredSlides: true,
      slidesPerView: 'auto',
      coverflowEffect: {
        rotate: 50,
        stretch: 0,
        depth: 100,
        modifier: 1,
        slideShadows: true
      },
      pagination: {
        el: '.swiper-pagination'
      }
    });
  }

  // 3D cube effect
  // --------------------------------------------------------------------
  if (swiper3dCubeEffect) {
    new Swiper(swiper3dCubeEffect, {
      effect: 'cube',
      grabCursor: true,
      cubeEffect: {
        shadow: true,
        slideShadows: true,
        shadowScale: 0.94,
        shadowOffset: 20
      },
      pagination: {
        el: '.swiper-pagination'
      }
    });
  }

  // 3D flip effect
  // --------------------------------------------------------------------
  if (swiper3dFlipEffect) {
    new Swiper(swiper3dFlipEffect, {
      effect: 'flip',
      grabCursor: true,
      pagination: {
        el: '.swiper-pagination'
      },
      navigation: {
        prevEl: '.swiper-button-prev',
        nextEl: '.swiper-button-next'
      }
    });
  }

  // Gallery effect
  // --------------------------------------------------------------------
  if (galleryThumbs) {
    galleryInstance = new Swiper(galleryThumbs, {
      spaceBetween: 10,
      slidesPerView: 4,
      freeMode: true,
      watchSlidesVisibility: true,
      watchSlidesProgress: true
    });
  }

  if (galleryTop) {
    new Swiper(galleryTop, {
      spaceBetween: 10,
      navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev'
      },
      thumbs: {
        swiper: galleryInstance
      }
    });
  }
})();
