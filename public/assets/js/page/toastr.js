"use strict";

$("#toastr-1").click(function () {
  iziToast.info({
    title: 'Hello, world!',
    message: 'This awesome plugin is made iziToast toastr',
    position: 'topRight'
  });
});

$("#toastr-2").click(function () {
  iziToast.success({
    title: 'Hello, world!',
    message: 'This awesome plugin is made by iziToast',
    position: 'topRight'
  });
});

$("#toastr-3").click(function () {
  iziToast.warning({
    title: 'Hello, world!',
    message: 'This awesome plugin is made by iziToast',
    position: 'topRight'
  });
});

$("#toastr-4").click(function () {
  iziToast.error({
    title: 'Hello, world!',
    message: 'This awesome plugin is made by iziToast',
    position: 'topRight'
  });
});

$("#toastr-5").click(function () {
  iziToast.show({
    title: 'Hello, world!',
    message: 'This awesome plugin is made by iziToast',
    position: 'bottomRight'
  });
});

$("#toastr-6").click(function () {
  iziToast.show({
    title: 'Hello, world!',
    message: 'This awesome plugin is made by iziToast',
    position: 'bottomCenter'
  });
});

$("#toastr-7").click(function () {
  iziToast.show({
    title: 'Hello, world!',
    message: 'This awesome plugin is made by iziToast',
    position: 'bottomLeft'
  });
});

$("#toastr-8").click(function () {
  iziToast.show({
    title: 'Hello, world!',
    message: 'This awesome plugin is made by iziToast',
    position: 'topCenter'
  });
});
