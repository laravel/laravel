/* This file is based on WebProfilerBundle/Resources/views/Profiler/base_js.html.twig.
   If you make any change in this file, verify the same change is needed in the other file. */
/*<![CDATA[*/
(function() {
    "use strict";

    if ('classList' in document.documentElement) {
        var hasClass = function (el, cssClass) { return el.classList.contains(cssClass); };
        var removeClass = function(el, cssClass) { el.classList.remove(cssClass); };
        var addClass = function(el, cssClass) { el.classList.add(cssClass); };
        var toggleClass = function(el, cssClass) { el.classList.toggle(cssClass); };
    } else {
        var hasClass = function (el, cssClass) { return el.className.match(new RegExp('\\b' + cssClass + '\\b')); };
        var removeClass = function(el, cssClass) { el.className = el.className.replace(new RegExp('\\b' + cssClass + '\\b'), ' '); };
        var addClass = function(el, cssClass) { if (!hasClass(el, cssClass)) { el.className += " " + cssClass; } };
        var toggleClass = function(el, cssClass) { hasClass(el, cssClass) ? removeClass(el, cssClass) : addClass(el, cssClass); };
    }

    var addEventListener;

    var el = document.createElement('div');
    if (!('addEventListener' in el)) {
        addEventListener = function (element, eventName, callback) {
            element.attachEvent('on' + eventName, callback);
        };
    } else {
        addEventListener = function (element, eventName, callback) {
            element.addEventListener(eventName, callback, false);
        };
    }

    if (navigator.clipboard) {
        document.querySelectorAll('[data-clipboard-text]').forEach(function(element) {
            removeClass(element, 'hidden');
            element.addEventListener('click', function() {
                navigator.clipboard.writeText(element.getAttribute('data-clipboard-text'));
            })
        });
    }

    (function createTabs() {
        /* the accessibility options of this component have been defined according to: */
        /* www.w3.org/WAI/ARIA/apg/example-index/tabs/tabs-manual.html */
        var tabGroups = document.querySelectorAll('.sf-tabs:not([data-processed=true])');

        /* create the tab navigation for each group of tabs */
        for (var i = 0; i < tabGroups.length; i++) {
            var tabs = tabGroups[i].querySelectorAll(':scope > .tab');
            var tabNavigation = document.createElement('div');
            tabNavigation.className = 'tab-navigation';
            tabNavigation.setAttribute('role', 'tablist');

            var selectedTabId = 'tab-' + i + '-0'; /* select the first tab by default */
            for (var j = 0; j < tabs.length; j++) {
                var tabId = 'tab-' + i + '-' + j;
                var tabTitle = tabs[j].querySelector('.tab-title').innerHTML;

                var tabNavigationItem = document.createElement('button');
                addClass(tabNavigationItem, 'tab-control');
                tabNavigationItem.setAttribute('data-tab-id', tabId);
                tabNavigationItem.setAttribute('role', 'tab');
                tabNavigationItem.setAttribute('aria-controls', tabId);
                if (hasClass(tabs[j], 'active')) { selectedTabId = tabId; }
                if (hasClass(tabs[j], 'disabled')) {
                    addClass(tabNavigationItem, 'disabled');
                }
                tabNavigationItem.innerHTML = tabTitle;
                tabNavigation.appendChild(tabNavigationItem);

                var tabContent = tabs[j].querySelector('.tab-content');
                tabContent.parentElement.setAttribute('id', tabId);
            }

            tabGroups[i].insertBefore(tabNavigation, tabGroups[i].firstChild);
            addClass(document.querySelector('[data-tab-id="' + selectedTabId + '"]'), 'active');
        }

        /* display the active tab and add the 'click' event listeners */
        for (i = 0; i < tabGroups.length; i++) {
            tabNavigation = tabGroups[i].querySelectorAll(':scope > .tab-navigation .tab-control');

            for (j = 0; j < tabNavigation.length; j++) {
                tabId = tabNavigation[j].getAttribute('data-tab-id');
                var tabPanel = document.getElementById(tabId);
                tabPanel.setAttribute('role', 'tabpanel');
                tabPanel.setAttribute('aria-labelledby', tabId);
                tabPanel.querySelector('.tab-title').className = 'hidden';

                if (hasClass(tabNavigation[j], 'active')) {
                    tabPanel.className = 'block';
                    tabNavigation[j].setAttribute('aria-selected', 'true');
                    tabNavigation[j].removeAttribute('tabindex');
                } else {
                    tabPanel.className = 'hidden';
                    tabNavigation[j].removeAttribute('aria-selected');
                    tabNavigation[j].setAttribute('tabindex', '-1');
                }

                tabNavigation[j].addEventListener('click', function(e) {
                    var activeTab = e.target || e.srcElement;

                    /* needed because when the tab contains HTML contents, user can click */
                    /* on any of those elements instead of their parent '<button>' element */
                    while (activeTab.tagName.toLowerCase() !== 'button') {
                        activeTab = activeTab.parentNode;
                    }

                    /* get the full list of tabs through the parent of the active tab element */
                    var tabNavigation = activeTab.parentNode.children;
                    for (var k = 0; k < tabNavigation.length; k++) {
                        var tabId = tabNavigation[k].getAttribute('data-tab-id');
                        document.getElementById(tabId).className = 'hidden';
                        removeClass(tabNavigation[k], 'active');
                        tabNavigation[k].removeAttribute('aria-selected');
                        tabNavigation[k].setAttribute('tabindex', '-1');
                    }

                    addClass(activeTab, 'active');
                    activeTab.setAttribute('aria-selected', 'true');
                    activeTab.removeAttribute('tabindex');
                    var activeTabId = activeTab.getAttribute('data-tab-id');
                    document.getElementById(activeTabId).className = 'block';
                });
            }

            tabGroups[i].setAttribute('data-processed', 'true');
        }
    })();

    (function createToggles() {
        var toggles = document.querySelectorAll('.sf-toggle:not([data-processed=true])');

        for (var i = 0; i < toggles.length; i++) {
            var elementSelector = toggles[i].getAttribute('data-toggle-selector');
            var element = document.querySelector(elementSelector);

            addClass(element, 'sf-toggle-content');

            if (toggles[i].hasAttribute('data-toggle-initial') && toggles[i].getAttribute('data-toggle-initial') == 'display') {
                addClass(toggles[i], 'sf-toggle-on');
                addClass(element, 'sf-toggle-visible');
            } else {
                addClass(toggles[i], 'sf-toggle-off');
                addClass(element, 'sf-toggle-hidden');
            }

            addEventListener(toggles[i], 'click', function(e) {
                var toggle = e.currentTarget;

                if (e.target.closest('a, span[data-clipboard-text], .sf-toggle') !== toggle) {
                    return;
                }

                e.preventDefault();

                if ('' !== window.getSelection().toString()) {
                    /* Don't do anything on text selection */
                    return;
                }

                var element = document.querySelector(toggle.getAttribute('data-toggle-selector'));

                toggleClass(toggle, 'sf-toggle-on');
                toggleClass(toggle, 'sf-toggle-off');
                toggleClass(element, 'sf-toggle-hidden');
                toggleClass(element, 'sf-toggle-visible');

                /* the toggle doesn't change its contents when clicking on it */
                if (!toggle.hasAttribute('data-toggle-alt-content')) {
                    return;
                }

                if (!toggle.hasAttribute('data-toggle-original-content')) {
                    toggle.setAttribute('data-toggle-original-content', toggle.innerHTML);
                }

                var currentContent = toggle.innerHTML;
                var originalContent = toggle.getAttribute('data-toggle-original-content');
                var altContent = toggle.getAttribute('data-toggle-alt-content');
                toggle.innerHTML = currentContent !== altContent ? altContent : originalContent;
            });

            toggles[i].setAttribute('data-processed', 'true');
        }
    })();

    (function createFilters() {
        document.querySelectorAll('[data-filters] [data-filter]').forEach(function (filter) {
            var filters = filter.closest('[data-filters]'),
                type = 'choice',
                name = filter.dataset.filter,
                ucName = name.charAt(0).toUpperCase()+name.slice(1),
                list = document.createElement('ul'),
                values = filters.dataset['filter'+ucName] || filters.querySelectorAll('[data-filter-'+name+']'),
                labels = {},
                defaults = null,
                indexed = {},
                processed = {};
            if (typeof values === 'string') {
                type = 'level';
                labels = values.split(',');
                values = values.toLowerCase().split(',');
                defaults = values.length - 1;
            }
            addClass(list, 'filter-list');
            addClass(list, 'filter-list-'+type);
            values.forEach(function (value, i) {
                if (value instanceof HTMLElement) {
                    value = value.dataset['filter'+ucName];
                }
                if (value in processed) {
                    return;
                }
                var option = document.createElement('li'),
                    label = i in labels ? labels[i] : value,
                    active = false,
                    matches;
                if ('' === label) {
                    option.innerHTML = '<em>(none)</em>';
                } else {
                    option.innerText = label;
                }
                option.dataset.filter = value;
                option.setAttribute('title', 1 === (matches = filters.querySelectorAll('[data-filter-'+name+'="'+value+'"]').length) ? 'Matches 1 row' : 'Matches '+matches+' rows');
                indexed[value] = i;
                list.appendChild(option);
                addEventListener(option, 'click', function () {
                    if ('choice' === type) {
                        filters.querySelectorAll('[data-filter-'+name+']').forEach(function (row) {
                            if (option.dataset.filter === row.dataset['filter'+ucName]) {
                                toggleClass(row, 'filter-hidden-'+name);
                            }
                        });
                        toggleClass(option, 'active');
                    } else if ('level' === type) {
                        if (i === this.parentNode.querySelectorAll('.active').length - 1) {
                            return;
                        }
                        this.parentNode.querySelectorAll('li').forEach(function (currentOption, j) {
                            if (j <= i) {
                                addClass(currentOption, 'active');
                                if (i === j) {
                                    addClass(currentOption, 'last-active');
                                } else {
                                    removeClass(currentOption, 'last-active');
                                }
                            } else {
                                removeClass(currentOption, 'active');
                                removeClass(currentOption, 'last-active');
                            }
                        });
                        filters.querySelectorAll('[data-filter-'+name+']').forEach(function (row) {
                            if (i < indexed[row.dataset['filter'+ucName]]) {
                                addClass(row, 'filter-hidden-'+name);
                            } else {
                                removeClass(row, 'filter-hidden-'+name);
                            }
                        });
                    }
                });
                if ('choice' === type) {
                    active = null === defaults || 0 <= defaults.indexOf(value);
                } else if ('level' === type) {
                    active = i <= defaults;
                    if (active && i === defaults) {
                        addClass(option, 'last-active');
                    }
                }
                if (active) {
                    addClass(option, 'active');
                } else {
                    filters.querySelectorAll('[data-filter-'+name+'="'+value+'"]').forEach(function (row) {
                        toggleClass(row, 'filter-hidden-'+name);
                    });
                }
                processed[value] = true;
            });

            if (1 < list.childNodes.length) {
                filter.appendChild(list);
                filter.dataset.filtered = '';
            }
        });
    })();
})();
/*]]>*/
