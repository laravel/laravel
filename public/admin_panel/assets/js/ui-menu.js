/**
 * Menu
 */

'use strict';

(function () {
  // ? This JS is for menu demo purpose only

  // Vertical
  const menu1 = document.querySelector('#menu-1'),
    menu1Btn = document.querySelector('#menu-1-toggle-collapsed');

  if (menu1) {
    new Menu(menu1);
  }
  if (menu1Btn) {
    menu1Btn.onclick = function () {
      menu1.classList.toggle('menu-collapsed');
    };
  }

  // Horizontal
  const menu2 = document.querySelector('#menu-2');
  if (menu2) {
    new Menu(menu2, {
      orientation: 'horizontal'
    });
  }

  // Horizontal (Show dropdown on hover)
  const menu3 = document.querySelector('#menu-3');
  if (menu3) {
    new Menu(menu3, {
      orientation: 'horizontal',
      showDropdownOnHover: true
    });
  }

  // No animation
  const menu5 = document.querySelector('#menu-5'),
    menu5Btn = document.querySelector('#menu-5-toggle-collapsed');
  if (menu5) {
    new Menu(menu5, {
      animate: false
    });
  }

  if (menu5Btn) {
    menu5Btn.onclick = function () {
      menu5.classList.toggle('menu-collapsed');
    };
  }
  const menu6 = document.querySelector('#menu-6');
  if (menu6) {
    new Menu(menu6, {
      orientation: 'horizontal',
      animate: false,
      closeChildren: true
    });
  }

  // No accordion
  const menu7 = document.querySelector('#menu-7'),
    menu7Btn = document.querySelector('#menu-7-toggle-collapsed');
  if (menu7) {
    new Menu(menu7, {
      accordion: false
    });
  }
  if (menu7Btn) {
    menu7Btn.onclick = function () {
      menu7.classList.toggle('menu-collapsed');
    };
  }

  const menu8 = document.querySelector('#menu-8');
  if (menu8) {
    new Menu(menu8, {
      orientation: 'horizontal',
      accordion: false
    });
  }

  // Elements
  const menus9List = document.querySelectorAll('.menus-9'),
    menu9Btn = document.querySelector('#menus-9-toggle-collapsed');
  if (menus9List) {
    menus9List.forEach(e => {
      new Menu(e);
    });
  }
  if (menu9Btn) {
    menu9Btn.onclick = function () {
      menus9List.forEach(e => {
        e.classList.toggle('menu-collapsed');
      });
    };
  }

  // Colors (vertical)
  const menus10List = document.querySelectorAll('.menus-10'),
    menu10Btn = document.querySelector('#menus-10-toggle-collapsed');
  if (menus10List) {
    menus10List.forEach(e => {
      new Menu(e);
    });
  }
  if (menu10Btn) {
    menu10Btn.onclick = function () {
      menus10List.forEach(e => {
        e.classList.toggle('menu-collapsed');
      });
    };
  }

  // Colors (horizontal)
  const menus11List = document.querySelectorAll('.menus-11');
  if (menus11List) {
    menus11List.forEach(e => {
      new Menu(e, {
        orientation: 'horizontal'
      });
    });
  }

  // With background (For Docs)
  const menus12List = document.querySelectorAll('.menus-12'),
    menu12Btn = document.querySelector('#menus-12-toggle-collapsed');
  if (menus12List) {
    menus12List.forEach(e => {
      new Menu(e);
    });
  }
  if (menu12Btn) {
    menu12Btn.onclick = function () {
      menus12List.forEach(e => {
        e.classList.toggle('menu-collapsed');
      });
    };
  }
})();
