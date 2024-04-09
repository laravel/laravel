/**
 * Config
 * -------------------------------------------------------------------------------------
 * ! IMPORTANT: Make sure you clear the browser local storage In order to see the config changes in the template.
 * ! To clear local storage: (https://www.leadshook.com/help/how-to-clear-local-storage-in-google-chrome-browser/).
 */

'use strict';

// JS global variables
let config = {
  colors: {
    primary: '#5a8dee',
    secondary: '#69809a',
    success: '#39da8a',
    info: '#00cfdd',
    warning: '#fdac41',
    danger: '#ff5b5c',
    dark: '#495563',
    black: '#000',
    white: '#fff',
    cardColor: '#fff',
    bodyBg: '#f2f2f6',
    bodyColor: '#677788',
    headingColor: '#516377',
    textMuted: '#a8b1bb',
    borderColor: '#e9ecee'
  },
  colors_label: {
    primary: '#5a8dee29',
    secondary: '#69809a29',
    success: '#39da8a29',
    info: '#00cfdd29',
    warning: '#fdac4129',
    danger: '#ff5b5c29',
    dark: '#49556329'
  },
  colors_dark: {
    cardColor: '#283144',
    bodyBg: '#1c222f',
    bodyColor: '#a1b0cb',
    headingColor: '#d8deea',
    textMuted: '#8295ba',
    borderColor: '#36445d'
  },
  enableMenuLocalStorage: true // Enable menu state with local storage support
};

let assetsPath = document.documentElement.getAttribute('data-assets-path'),
  templateName = document.documentElement.getAttribute('data-template'),
  rtlSupport = true; // set true for rtl support (rtl + ltr), false for ltr only.

/**
 * TemplateCustomizer
 * ! You must use(include) template-customizer.js to use TemplateCustomizer settings
 * -----------------------------------------------------------------------------------------------
 */

// To use more themes, just push it to THEMES object.

/* TemplateCustomizer.THEMES.push({
  name: 'theme-raspberry',
  title: 'Raspberry'
}); */

// To add more languages, just push it to LANGUAGES object.
/*
TemplateCustomizer.LANGUAGES.fr = { ... };
*/

/**
 * TemplateCustomizer settings
 * -------------------------------------------------------------------------------------
 * cssPath: Core CSS file path
 * themesPath: Theme CSS file path
 * displayCustomizer: true(Show customizer), false(Hide customizer)
 * lang: To set default language, Add more langues and set default. Fallback language is 'en'
 * controls: [ 'rtl','style','layoutType','showDropdownOnHover','layoutNavbarFixed','layoutFooterFixed','themes'] | Show/Hide customizer controls
 * defaultTheme: 0(Default), 1(Semi Dark), 2(Bordered)
 * defaultStyle: 'light', 'dark' (Mode)
 * defaultTextDir: 'ltr', 'rtl' (rtlSupport must be true for rtl mode)
 * defaultLayoutType: 'static', 'fixed'
 * defaultMenuCollapsed: true, false
 * defaultNavbarFixed: true, false
 * defaultFooterFixed: true, false
 * defaultShowDropdownOnHover : true, false (for horizontal layout only)
 */

if (typeof TemplateCustomizer !== 'undefined') {
  window.templateCustomizer = new TemplateCustomizer({
    cssPath: assetsPath + 'vendor/css' + (rtlSupport ? '/rtl' : '') + '/',
    themesPath: assetsPath + 'vendor/css' + (rtlSupport ? '/rtl' : '') + '/',
    displayCustomizer: true,
    // lang: 'fr',
    // defaultTheme: 2,
    // defaultStyle: 'light',
    // defaultTextDir: 'ltr',
    // defaultLayoutType: 'fixed',
    // defaultMenuCollapsed: true,
    // defaultNavbarFixed: true,
    // defaultFooterFixed: false
    defaultShowDropdownOnHover: true
    // controls: [
    //   'rtl',
    //   'style',
    //   'layoutType',
    //   'showDropdownOnHover',
    //   'layoutNavbarFixed',
    //   'layoutFooterFixed',
    //   'themes'
    // ],
  });
}
