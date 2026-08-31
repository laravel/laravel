<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\VarDumper\Dumper;

use Symfony\Component\VarDumper\Cloner\Cursor;
use Symfony\Component\VarDumper\Cloner\Data;

/**
 * HtmlDumper dumps variables as HTML.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
class HtmlDumper extends CliDumper
{
    /** @var callable|resource|string|null */
    public static $defaultOutput = 'php://output';

    protected static $themes = [
        'dark' => [
            'default' => 'background-color:#18171B; color:#FF8400; line-height:1.2em; font:12px Menlo, Monaco, Consolas, monospace; word-wrap: break-word; white-space: pre-wrap; position:relative; z-index:99999; word-break: break-all',
            'num' => 'font-weight:bold; color:#1299DA',
            'const' => 'font-weight:bold',
            'virtual' => 'font-style:italic',
            'str' => 'font-weight:bold; color:#56DB3A',
            'note' => 'color:#1299DA',
            'ref' => 'color:#A0A0A0',
            'public' => 'color:#FFFFFF',
            'protected' => 'color:#FFFFFF',
            'private' => 'color:#FFFFFF',
            'meta' => 'color:#B729D9',
            'key' => 'color:#56DB3A',
            'index' => 'color:#1299DA',
            'ellipsis' => 'color:#FF8400',
            'ns' => 'user-select:none;',
        ],
        'light' => [
            'default' => 'background:none; color:#CC7832; line-height:1.2em; font:12px Menlo, Monaco, Consolas, monospace; word-wrap: break-word; white-space: pre-wrap; position:relative; z-index:99999; word-break: break-all',
            'num' => 'font-weight:bold; color:#1299DA',
            'const' => 'font-weight:bold',
            'virtual' => 'font-style:italic',
            'str' => 'font-weight:bold; color:#629755;',
            'note' => 'color:#6897BB',
            'ref' => 'color:#6E6E6E',
            'public' => 'color:#262626',
            'protected' => 'color:#262626',
            'private' => 'color:#262626',
            'meta' => 'color:#B729D9',
            'key' => 'color:#789339',
            'index' => 'color:#1299DA',
            'ellipsis' => 'color:#CC7832',
            'ns' => 'user-select:none;',
        ],
    ];

    protected ?string $dumpHeader = null;
    protected string $dumpPrefix = '<pre class=sf-dump id=%s data-indent-pad="%s">';
    protected string $dumpSuffix = '</pre><script>Sfdump(%s)</script>';
    protected string $dumpId;
    protected bool $colors = true;
    protected $headerIsDumped = false;
    protected int $lastDepth = -1;

    private array $displayOptions = [
        'maxDepth' => 1,
        'maxStringLength' => 160,
        'fileLinkFormat' => null,
    ];
    private array $extraDisplayOptions = [];

    public function __construct($output = null, ?string $charset = null, int $flags = 0)
    {
        AbstractDumper::__construct($output, $charset, $flags);
        $this->dumpId = 'sf-dump-'.mt_rand();
        $this->displayOptions['fileLinkFormat'] = \ini_get('xdebug.file_link_format') ?: get_cfg_var('xdebug.file_link_format');
        $this->styles = static::$themes['dark'] ?? self::$themes['dark'];
    }

    public function setStyles(array $styles): void
    {
        $this->headerIsDumped = false;
        $this->styles = $styles + $this->styles;
    }

    public function setTheme(string $themeName): void
    {
        if (!isset(static::$themes[$themeName])) {
            throw new \InvalidArgumentException(\sprintf('Theme "%s" does not exist in class "%s".', $themeName, static::class));
        }

        $this->setStyles(static::$themes[$themeName]);
    }

    /**
     * Configures display options.
     *
     * @param array $displayOptions A map of display options to customize the behavior
     */
    public function setDisplayOptions(array $displayOptions): void
    {
        $this->headerIsDumped = false;
        $this->displayOptions = $displayOptions + $this->displayOptions;
    }

    /**
     * Sets an HTML header that will be dumped once in the output stream.
     */
    public function setDumpHeader(?string $header): void
    {
        $this->dumpHeader = $header;
    }

    /**
     * Sets an HTML prefix and suffix that will encapse every single dump.
     */
    public function setDumpBoundaries(string $prefix, string $suffix): void
    {
        $this->dumpPrefix = $prefix;
        $this->dumpSuffix = $suffix;
    }

    public function dump(Data $data, $output = null, array $extraDisplayOptions = []): ?string
    {
        $this->extraDisplayOptions = $extraDisplayOptions;
        $result = parent::dump($data, $output);
        $this->dumpId = 'sf-dump-'.mt_rand();

        return $result;
    }

    /**
     * Dumps the HTML header.
     */
    protected function getDumpHeader(): string
    {
        $this->headerIsDumped = $this->outputStream ?? $this->lineDumper;

        if (null !== $this->dumpHeader) {
            return $this->dumpHeader;
        }

        $line = str_replace('{$options}', json_encode($this->displayOptions, \JSON_FORCE_OBJECT), <<<'EOHTML'
            <script>
            Sfdump = window.Sfdump || (function (doc) {
            doc.documentElement.classList.add('sf-js-enabled');

            var rxEsc = /([.*+?^${}()|\[\]\/\\])/g,
                idRx = /\bsf-dump-\d+-ref[012]\w+\b/,
                keyHint = 0 <= navigator.platform.toUpperCase().indexOf('MAC') ? 'Cmd' : 'Ctrl',
                addEventListener = function (e, n, cb) {
                    e.addEventListener(n, cb, false);
                };

            if (!doc.addEventListener) {
                addEventListener = function (element, eventName, callback) {
                    element.attachEvent('on' + eventName, function (e) {
                        e.preventDefault = function () {e.returnValue = false;};
                        e.target = e.srcElement;
                        callback(e);
                    });
                };
            }

            function toggle(a, recursive) {
                var s = a.nextSibling || {}, oldClass = s.className, arrow, newClass;

                if (/\bsf-dump-compact\b/.test(oldClass)) {
                    arrow = '▼';
                    newClass = 'sf-dump-expanded';
                } else if (/\bsf-dump-expanded\b/.test(oldClass)) {
                    arrow = '▶';
                    newClass = 'sf-dump-compact';
                } else {
                    return false;
                }

                if (doc.createEvent && s.dispatchEvent) {
                    var event = doc.createEvent('Event');
                    event.initEvent('sf-dump-expanded' === newClass ? 'sfbeforedumpexpand' : 'sfbeforedumpcollapse', true, false);

                    s.dispatchEvent(event);
                }

                a.lastChild.innerHTML = arrow;
                s.className = s.className.replace(/\bsf-dump-(compact|expanded)\b/, newClass);

                if (recursive) {
                    try {
                        a = s.querySelectorAll('.'+oldClass);
                        for (s = 0; s < a.length; ++s) {
                            if (-1 == a[s].className.indexOf(newClass)) {
                                a[s].className = newClass;
                                a[s].previousSibling.lastChild.innerHTML = arrow;
                            }
                        }
                    } catch (e) {
                    }
                }

                return true;
            };

            function collapse(a, recursive) {
                var s = a.nextSibling || {}, oldClass = s.className;

                if (/\bsf-dump-expanded\b/.test(oldClass)) {
                    toggle(a, recursive);

                    return true;
                }

                return false;
            };

            function expand(a, recursive) {
                var s = a.nextSibling || {}, oldClass = s.className;

                if (/\bsf-dump-compact\b/.test(oldClass)) {
                    toggle(a, recursive);

                    return true;
                }

                return false;
            };

            function collapseAll(root) {
                var a = root.querySelector('a.sf-dump-toggle');
                if (a) {
                    collapse(a, true);
                    expand(a);

                    return true;
                }

                return false;
            }

            function reveal(node) {
                var previous, parents = [];

                while ((node = node.parentNode || {}) && (previous = node.previousSibling) && 'A' === previous.tagName) {
                    parents.push(previous);
                }

                if (0 !== parents.length) {
                    parents.forEach(function (parent) {
                        expand(parent);
                    });

                    return true;
                }

                return false;
            }

            function highlight(root, activeNode, nodes) {
                resetHighlightedNodes(root);

                Array.from(nodes||[]).forEach(function (node) {
                    if (!/\bsf-dump-highlight\b/.test(node.className)) {
                        node.className = node.className + ' sf-dump-highlight';
                    }
                });

                if (!/\bsf-dump-highlight-active\b/.test(activeNode.className)) {
                    activeNode.className = activeNode.className + ' sf-dump-highlight-active';
                }
            }

            function resetHighlightedNodes(root) {
                Array.from(root.querySelectorAll('.sf-dump-str, .sf-dump-key, .sf-dump-public, .sf-dump-protected, .sf-dump-private')).forEach(function (strNode) {
                    strNode.className = strNode.className.replace(/\bsf-dump-highlight\b/, '');
                    strNode.className = strNode.className.replace(/\bsf-dump-highlight-active\b/, '');
                });
            }

            return function (root, x) {
                root = doc.getElementById(root);

                var indentRx = new RegExp('^('+(root.getAttribute('data-indent-pad') || '  ').replace(rxEsc, '\\$1')+')+', 'm'),
                    options = {$options},
                    elt = root.getElementsByTagName('A'),
                    len = elt.length,
                    i = 0, s, h,
                    t = [];

                while (i < len) t.push(elt[i++]);

                for (i in x) {
                    options[i] = x[i];
                }

                function a(e, f) {
                    addEventListener(root, e, function (e, n) {
                        if ('A' == e.target.tagName) {
                            f(e.target, e);
                        } else if ('A' == e.target.parentNode.tagName) {
                            f(e.target.parentNode, e);
                        } else {
                            n = /\bsf-dump-ellipsis\b/.test(e.target.className) ? e.target.parentNode : e.target;

                            if ((n = n.nextElementSibling) && 'A' == n.tagName) {
                                if (!/\bsf-dump-toggle\b/.test(n.className)) {
                                    n = n.nextElementSibling || n;
                                }

                                f(n, e, true);
                            }
                        }
                    });
                };
                function isCtrlKey(e) {
                    return e.ctrlKey || e.metaKey;
                }
                function xpathString(str) {
                    var parts = str.match(/[^'"]+|['"]/g).map(function (part) {
                        if ("'" == part)  {
                            return '"\'"';
                        }
                        if ('"' == part) {
                            return "'\"'";
                        }

                        return "'" + part + "'";
                    });

                    return "concat(" + parts.join(",") + ", '')";
                }
                function xpathHasClass(className) {
                    return "contains(concat(' ', normalize-space(@class), ' '), ' " + className +" ')";
                }
                a('mouseover', function (a, e, c) {
                    if (c) {
                        e.target.style.cursor = "pointer";
                    }
                });
                a('click', function (a, e, c) {
                    if (/\bsf-dump-toggle\b/.test(a.className)) {
                        e.preventDefault();
                        if (!toggle(a, isCtrlKey(e))) {
                            var r = doc.getElementById(a.getAttribute('href').slice(1)),
                                s = r.previousSibling,
                                f = r.parentNode,
                                t = a.parentNode;
                            t.replaceChild(r, a);
                            f.replaceChild(a, s);
                            t.insertBefore(s, r);
                            f = f.firstChild.nodeValue.match(indentRx);
                            t = t.firstChild.nodeValue.match(indentRx);
                            if (f && t && f[0] !== t[0]) {
                                r.innerHTML = r.innerHTML.replace(new RegExp('^'+f[0].replace(rxEsc, '\\$1'), 'mg'), t[0]);
                            }
                            if (/\bsf-dump-compact\b/.test(r.className)) {
                                toggle(s, isCtrlKey(e));
                            }
                        }

                        if (c) {
                        } else if (doc.getSelection) {
                            try {
                                doc.getSelection().removeAllRanges();
                            } catch (e) {
                                doc.getSelection().empty();
                            }
                        } else {
                            doc.selection.empty();
                        }
                    } else if (/\bsf-dump-str-toggle\b/.test(a.className)) {
                        e.preventDefault();
                        e = a.parentNode.parentNode;
                        e.className = e.className.replace(/\bsf-dump-str-(expand|collapse)\b/, a.parentNode.className);
                    }
                });

                elt = root.getElementsByTagName('SAMP');
                len = elt.length;
                i = 0;

                while (i < len) t.push(elt[i++]);
                len = t.length;

                for (i = 0; i < len; ++i) {
                    elt = t[i];
                    if ('SAMP' == elt.tagName) {
                        a = elt.previousSibling || {};
                        if ('A' != a.tagName) {
                            a = doc.createElement('A');
                            a.className = 'sf-dump-ref';
                            elt.parentNode.insertBefore(a, elt);
                        } else {
                            a.innerHTML += ' ';
                        }
                        a.title = (a.title ? a.title+'\n[' : '[')+keyHint+'+click] Expand all children';
                        a.innerHTML += elt.className == 'sf-dump-compact' ? '<span>▶</span>' : '<span>▼</span>';
                        a.className += ' sf-dump-toggle';

                        x = 1;
                        if ('sf-dump' != elt.parentNode.className) {
                            x += elt.parentNode.getAttribute('data-depth')/1;
                        }
                    } else if (/\bsf-dump-ref\b/.test(elt.className) && (a = elt.getAttribute('href'))) {
                        a = a.slice(1);
                        elt.className += ' sf-dump-hover';
                        elt.className += ' '+a;

                        if (/[\[{]$/.test(elt.previousSibling.nodeValue)) {
                            a = a != elt.nextSibling.id && doc.getElementById(a);
                            try {
                                s = a.nextSibling;
                                elt.appendChild(a);
                                s.parentNode.insertBefore(a, s);
                                if (/^[@#]/.test(elt.innerHTML)) {
                                    elt.innerHTML += ' <span>▶</span>';
                                } else {
                                    elt.innerHTML = '<span>▶</span>';
                                    elt.className = 'sf-dump-ref';
                                }
                                elt.className += ' sf-dump-toggle';
                            } catch (e) {
                                if ('&' == elt.innerHTML.charAt(0)) {
                                    elt.innerHTML = '…';
                                    elt.className = 'sf-dump-ref';
                                }
                            }
                        }
                    }
                }

                if (doc.evaluate && Array.from && root.children.length > 1) {
                    root.setAttribute('tabindex', 0);

                    SearchState = function () {
                        this.nodes = [];
                        this.idx = 0;
                    };
                    SearchState.prototype = {
                        next: function () {
                            if (this.isEmpty()) {
                                return this.current();
                            }
                            this.idx = this.idx < (this.nodes.length - 1) ? this.idx + 1 : 0;

                            return this.current();
                        },
                        previous: function () {
                            if (this.isEmpty()) {
                                return this.current();
                            }
                            this.idx = this.idx > 0 ? this.idx - 1 : (this.nodes.length - 1);

                            return this.current();
                        },
                        isEmpty: function () {
                            return 0 === this.count();
                        },
                        current: function () {
                            if (this.isEmpty()) {
                                return null;
                            }
                            return this.nodes[this.idx];
                        },
                        reset: function () {
                            this.nodes = [];
                            this.idx = 0;
                        },
                        count: function () {
                            return this.nodes.length;
                        },
                    };

                    function showCurrent(state)
                    {
                        var currentNode = state.current(), currentRect, searchRect;
                        if (currentNode) {
                            reveal(currentNode);
                            highlight(root, currentNode, state.nodes);
                            if ('scrollIntoView' in currentNode) {
                                currentNode.scrollIntoView(true);
                                currentRect = currentNode.getBoundingClientRect();
                                searchRect = search.getBoundingClientRect();
                                if (currentRect.top < (searchRect.top + searchRect.height)) {
                                    window.scrollBy(0, -(searchRect.top + searchRect.height + 5));
                                }
                            }
                        }
                        counter.textContent = (state.isEmpty() ? 0 : state.idx + 1) + ' of ' + state.count();
                    }

                    var search = doc.createElement('div');
                    search.className = 'sf-dump-search-wrapper sf-dump-search-hidden';
                    search.innerHTML = '
                        <input type="text" class="sf-dump-search-input">
                        <span class="sf-dump-search-count">0 of 0<\/span>
                        <button type="button" class="sf-dump-search-input-previous" tabindex="-1">
                            <svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1683 1331l-166 165q-19 19-45 19t-45-19L896 965l-531 531q-19 19-45 19t-45-19l-166-165q-19-19-19-45.5t19-45.5l742-741q19-19 45-19t45 19l742 741q19 19 19 45.5t-19 45.5z"\/><\/svg>
                        <\/button>
                        <button type="button" class="sf-dump-search-input-next" tabindex="-1">
                            <svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1683 808l-742 741q-19 19-45 19t-45-19L109 808q-19-19-19-45.5t19-45.5l166-165q19-19 45-19t45 19l531 531 531-531q19-19 45-19t45 19l166 165q19 19 19 45.5t-19 45.5z"\/><\/svg>
                        <\/button>
                    ';
                    root.insertBefore(search, root.firstChild);

                    var state = new SearchState();
                    var searchInput = search.querySelector('.sf-dump-search-input');
                    var counter = search.querySelector('.sf-dump-search-count');
                    var searchInputTimer = 0;
                    var previousSearchQuery = '';

                    addEventListener(searchInput, 'keyup', function (e) {
                        var searchQuery = e.target.value;
                        /* Don't perform anything if the pressed key didn't change the query */
                        if (searchQuery === previousSearchQuery) {
                            return;
                        }
                        previousSearchQuery = searchQuery;
                        clearTimeout(searchInputTimer);
                        searchInputTimer = setTimeout(function () {
                            state.reset();
                            collapseAll(root);
                            resetHighlightedNodes(root);
                            if ('' === searchQuery) {
                                counter.textContent = '0 of 0';

                                return;
                            }

                            var classMatches = [
                                "sf-dump-str",
                                "sf-dump-key",
                                "sf-dump-public",
                                "sf-dump-protected",
                                "sf-dump-private",
                            ].map(xpathHasClass).join(' or ');

                            var xpathResult = doc.evaluate('.//span[' + classMatches + '][contains(translate(child::text(), ' + xpathString(searchQuery.toUpperCase()) + ', ' + xpathString(searchQuery.toLowerCase()) + '), ' + xpathString(searchQuery.toLowerCase()) + ')]', root, null, XPathResult.ORDERED_NODE_ITERATOR_TYPE, null);

                            while (node = xpathResult.iterateNext()) state.nodes.push(node);

                            showCurrent(state);
                        }, 400);
                    });

                    Array.from(search.querySelectorAll('.sf-dump-search-input-next, .sf-dump-search-input-previous')).forEach(function (btn) {
                        addEventListener(btn, 'click', function (e) {
                            e.preventDefault();
                            -1 !== e.target.className.indexOf('next') ? state.next() : state.previous();
                            searchInput.focus();
                            collapseAll(root);
                            showCurrent(state);
                        })
                    });

                    addEventListener(root, 'keydown', function (e) {
                        var isSearchActive = !/\bsf-dump-search-hidden\b/.test(search.className);
                        if ((114 === e.keyCode && !isSearchActive) || (isCtrlKey(e) && 70 === e.keyCode)) {
                            /* F3 or CMD/CTRL + F */
                            if (70 === e.keyCode && document.activeElement === searchInput) {
                               /*
                                * If CMD/CTRL + F is hit while having focus on search input,
                                * the user probably meant to trigger browser search instead.
                                * Let the browser execute its behavior:
                                */
                                return;
                            }

                            e.preventDefault();
                            search.className = search.className.replace(/\bsf-dump-search-hidden\b/, '');
                            searchInput.focus();
                        } else if (isSearchActive) {
                            if (27 === e.keyCode) {
                                /* ESC key */
                                search.className += ' sf-dump-search-hidden';
                                e.preventDefault();
                                resetHighlightedNodes(root);
                                searchInput.value = '';
                            } else if (
                                (isCtrlKey(e) && 71 === e.keyCode) /* CMD/CTRL + G */
                                || 13 === e.keyCode /* Enter */
                                || 114 === e.keyCode /* F3 */
                            ) {
                                e.preventDefault();
                                e.shiftKey ? state.previous() : state.next();
                                collapseAll(root);
                                showCurrent(state);
                            }
                        }
                    });
                }

                if (0 >= options.maxStringLength) {
                    return;
                }
                try {
                    elt = root.querySelectorAll('.sf-dump-str');
                    len = elt.length;
                    i = 0;
                    t = [];

                    while (i < len) t.push(elt[i++]);
                    len = t.length;

                    for (i = 0; i < len; ++i) {
                        elt = t[i];
                        s = elt.innerText || elt.textContent;
                        x = s.length - options.maxStringLength;
                        if (0 < x) {
                            h = elt.innerHTML;
                            elt[elt.innerText ? 'innerText' : 'textContent'] = s.substring(0, options.maxStringLength);
                            elt.className += ' sf-dump-str-collapse';
                            elt.innerHTML = '<span class=sf-dump-str-collapse>'+h+'<a class="sf-dump-ref sf-dump-str-toggle" title="Collapse"> ◀</a></span>'+
                                '<span class=sf-dump-str-expand>'+elt.innerHTML+'<a class="sf-dump-ref sf-dump-str-toggle" title="'+x+' remaining characters"> ▶</a></span>';
                        }
                    }
                } catch (e) {
                }
            };

            })(document);
            </script><style>
            .sf-js-enabled pre.sf-dump .sf-dump-compact,
            .sf-js-enabled .sf-dump-str-collapse .sf-dump-str-collapse,
            .sf-js-enabled .sf-dump-str-expand .sf-dump-str-expand {
                display: none;
            }
            .sf-dump-hover:hover {
                background-color: #B729D9;
                color: #FFF !important;
                border-radius: 2px;
            }
            pre.sf-dump {
                display: block;
                white-space: pre;
                padding: 5px;
                overflow: initial !important;
            }
            pre.sf-dump:after {
               content: "";
               visibility: hidden;
               display: block;
               height: 0;
               clear: both;
            }
            pre.sf-dump .sf-dump-ellipsization {
                display: inline-flex;
            }
            pre.sf-dump a {
                text-decoration: none;
                cursor: pointer;
                border: 0;
                outline: none;
                color: inherit;
            }
            pre.sf-dump img {
                max-width: 50em;
                max-height: 50em;
                margin: .5em 0 0 0;
                padding: 0;
                background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAAAAAA6mKC9AAAAHUlEQVQY02O8zAABilCaiQEN0EeA8QuUcX9g3QEAAjcC5piyhyEAAAAASUVORK5CYII=) #D3D3D3;
            }
            pre.sf-dump .sf-dump-ellipsis {
                text-overflow: ellipsis;
                white-space: nowrap;
                overflow: hidden;
            }
            pre.sf-dump .sf-dump-ellipsis-tail {
                flex-shrink: 0;
            }
            pre.sf-dump code {
                display:inline;
                padding:0;
                background:none;
            }
            .sf-dump-public.sf-dump-highlight,
            .sf-dump-protected.sf-dump-highlight,
            .sf-dump-private.sf-dump-highlight,
            .sf-dump-str.sf-dump-highlight,
            .sf-dump-key.sf-dump-highlight {
                background: rgba(111, 172, 204, 0.3);
                border: 1px solid #7DA0B1;
                border-radius: 3px;
            }
            .sf-dump-public.sf-dump-highlight-active,
            .sf-dump-protected.sf-dump-highlight-active,
            .sf-dump-private.sf-dump-highlight-active,
            .sf-dump-str.sf-dump-highlight-active,
            .sf-dump-key.sf-dump-highlight-active {
                background: rgba(253, 175, 0, 0.4);
                border: 1px solid #ffa500;
                border-radius: 3px;
            }
            pre.sf-dump .sf-dump-search-hidden {
                display: none !important;
            }
            pre.sf-dump .sf-dump-search-wrapper {
                font-size: 0;
                white-space: nowrap;
                margin-bottom: 5px;
                display: flex;
                position: -webkit-sticky;
                position: sticky;
                top: 5px;
            }
            pre.sf-dump .sf-dump-search-wrapper > * {
                vertical-align: top;
                box-sizing: border-box;
                height: 21px;
                font-weight: normal;
                border-radius: 0;
                background: #FFF;
                color: #757575;
                border: 1px solid #BBB;
            }
            pre.sf-dump .sf-dump-search-wrapper > input.sf-dump-search-input {
                padding: 3px;
                height: 21px;
                font-size: 12px;
                border-right: none;
                border-top-left-radius: 3px;
                border-bottom-left-radius: 3px;
                color: #000;
                min-width: 15px;
                width: 100%;
            }
            pre.sf-dump .sf-dump-search-wrapper > .sf-dump-search-input-next,
            pre.sf-dump .sf-dump-search-wrapper > .sf-dump-search-input-previous {
                background: #F2F2F2;
                outline: none;
                border-left: none;
                font-size: 0;
                line-height: 0;
            }
            pre.sf-dump .sf-dump-search-wrapper > .sf-dump-search-input-next {
                border-top-right-radius: 3px;
                border-bottom-right-radius: 3px;
            }
            pre.sf-dump .sf-dump-search-wrapper > .sf-dump-search-input-next > svg,
            pre.sf-dump .sf-dump-search-wrapper > .sf-dump-search-input-previous > svg {
                pointer-events: none;
                width: 12px;
                height: 12px;
            }
            pre.sf-dump .sf-dump-search-wrapper > .sf-dump-search-count {
                display: inline-block;
                padding: 0 5px;
                margin: 0;
                border-left: none;
                line-height: 21px;
                font-size: 12px;
            }
            EOHTML
        );

        foreach ($this->styles as $class => $style) {
            $line .= 'pre.sf-dump'.('default' === $class ? ', pre.sf-dump' : '').' .sf-dump-'.$class.'{'.$style.'}';
        }
        $line .= 'pre.sf-dump .sf-dump-ellipsis-note{'.$this->styles['note'].'}';

        return $this->dumpHeader = preg_replace('/\s+/', ' ', $line).'</style>'.$this->dumpHeader;
    }

    public function dumpString(Cursor $cursor, string $str, bool $bin, int $cut): void
    {
        if ('' === $str && isset($cursor->attr['img-data'], $cursor->attr['content-type'])) {
            $this->dumpKey($cursor);
            $this->line .= $this->style('default', $cursor->attr['img-size'] ?? '', []);
            $this->line .= $cursor->depth >= $this->displayOptions['maxDepth'] ? ' <samp class=sf-dump-compact>' : ' <samp class=sf-dump-expanded>';
            $this->endValue($cursor);
            $this->line .= $this->indentPad;
            $this->line .= \sprintf('<img src="data:%s;base64,%s" /></samp>', $cursor->attr['content-type'], base64_encode($cursor->attr['img-data']));
            $this->endValue($cursor);
        } else {
            parent::dumpString($cursor, $str, $bin, $cut);
        }
    }

    public function enterHash(Cursor $cursor, int $type, string|int|null $class, bool $hasChild): void
    {
        if (Cursor::HASH_OBJECT === $type) {
            $cursor->attr['depth'] = $cursor->depth;
        }
        parent::enterHash($cursor, $type, $class, false);

        if ($cursor->skipChildren || $cursor->depth >= $this->displayOptions['maxDepth']) {
            $cursor->skipChildren = false;
            $eol = ' class=sf-dump-compact>';
        } else {
            $this->expandNextHash = false;
            $eol = ' class=sf-dump-expanded>';
        }

        if ($hasChild) {
            $this->line .= '<samp data-depth='.($cursor->depth + 1);
            if ($cursor->refIndex) {
                $r = Cursor::HASH_OBJECT !== $type ? 1 - (Cursor::HASH_RESOURCE !== $type) : 2;
                $r .= $r && 0 < $cursor->softRefHandle ? $cursor->softRefHandle : $cursor->refIndex;

                $this->line .= \sprintf(' id=%s-ref%s', $this->dumpId, $r);
            }
            $this->line .= $eol;
            $this->dumpLine($cursor->depth);
        }
    }

    public function leaveHash(Cursor $cursor, int $type, string|int|null $class, bool $hasChild, int $cut): void
    {
        $this->dumpEllipsis($cursor, $hasChild, $cut);
        if ($hasChild) {
            $this->line .= '</samp>';
        }
        parent::leaveHash($cursor, $type, $class, $hasChild, 0);
    }

    protected function style(string $style, string $value, array $attr = []): string
    {
        if ('' === $value && ('label' !== $style || !isset($attr['file']) && !isset($attr['href']))) {
            return '';
        }

        $v = esc($value);

        if ('ref' === $style) {
            if (empty($attr['count'])) {
                return \sprintf('<a class=sf-dump-ref>%s</a>', $v);
            }
            $r = ('#' !== $v[0] ? 1 - ('@' !== $v[0]) : 2).substr($value, 1);

            return \sprintf('<a class=sf-dump-ref href=#%s-ref%s title="%d occurrences">%s</a>', $this->dumpId, $r, 1 + $attr['count'], $v);
        }

        $dumpClasses = ['sf-dump-'.$style];
        $dumpTitle = '';

        if ('const' === $style && isset($attr['value'])) {
            $dumpTitle = esc(\is_scalar($attr['value']) ? $attr['value'] : json_encode($attr['value']));
        } elseif ('public' === $style) {
            $dumpTitle = empty($attr['dynamic']) ? 'Public property' : 'Runtime added dynamic property';
        } elseif ('str' === $style && 1 < $attr['length']) {
            $dumpTitle = \sprintf('%d%s characters', $attr['length'], $attr['binary'] ? ' binary or non-UTF-8' : '');
        } elseif ('note' === $style && 0 < ($attr['depth'] ?? 0) && false !== $c = strrpos($value, '\\')) {
            $attr += [
                'ellipsis' => \strlen($value) - $c,
                'ellipsis-type' => 'note',
                'ellipsis-tail' => 1,
            ];
        } elseif ('protected' === $style) {
            $dumpTitle = 'Protected property';
        } elseif ('meta' === $style && isset($attr['title'])) {
            $dumpTitle = esc($this->utf8Encode($attr['title']));
        } elseif ('private' === $style) {
            $dumpTitle = \sprintf('Private property defined in class:&#10;`%s`', esc($this->utf8Encode($attr['class'])));
        }

        if (isset($attr['ellipsis'])) {
            $dumpClasses[] = 'sf-dump-ellipsization';
            $ellipsisClass = 'sf-dump-ellipsis';
            if (isset($attr['ellipsis-type'])) {
                $ellipsisClass .= ' sf-dump-ellipsis-'.$attr['ellipsis-type'];
            }
            $label = esc(substr($value, -$attr['ellipsis']));
            $dumpTitle = $v."\n".$dumpTitle;
            $v = \sprintf('<span class="%s">%s</span>', $ellipsisClass, substr($v, 0, -\strlen($label)));

            if (!empty($attr['ellipsis-tail'])) {
                $tail = \strlen(esc(substr($value, -$attr['ellipsis'], $attr['ellipsis-tail'])));
                $v .= \sprintf('<span class="%s">%s</span><span class="sf-dump-ellipsis-tail">%s</span>', $ellipsisClass, substr($label, 0, $tail), substr($label, $tail));
            } else {
                $v .= \sprintf('<span class="sf-dump-ellipsis-tail">%s</span>', $label);
            }
        }

        $map = static::$controlCharsMap;
        $v = \sprintf(
            '<span class=%s%s%1$s%s>%s</span>',
            1 === \count($dumpClasses) ? '' : '"',
            implode(' ', $dumpClasses),
            $dumpTitle ? ' title="'.$dumpTitle.'"' : '',
            preg_replace_callback(static::$controlCharsRx, static function ($c) use ($map) {
                $s = $b = '<span class="sf-dump-default';
                $c = $c[$i = 0];
                if ($ns = "\r" === $c[$i] || "\n" === $c[$i]) {
                    $s .= ' sf-dump-ns';
                }
                $s .= '">';
                do {
                    if (("\r" === $c[$i] || "\n" === $c[$i]) !== $ns) {
                        $s .= '</span>'.$b;
                        if ($ns = !$ns) {
                            $s .= ' sf-dump-ns';
                        }
                        $s .= '">';
                    }

                    $s .= $map[$c[$i]] ?? \sprintf('\x%02X', \ord($c[$i]));
                } while (isset($c[++$i]));

                return $s.'</span>';
            }, $v)
        );

        if (!($attr['binary'] ?? false)) {
            $v = preg_replace_callback(static::$unicodeCharsRx, static fn ($c) => '<span class=sf-dump-default>\u{'.strtoupper(dechex(mb_ord($c[0]))).'}</span>', $v);
        }

        if (isset($attr['file']) && $href = $this->getSourceLink($attr['file'], $attr['line'] ?? 0)) {
            $attr['href'] = $href;
        }
        if (isset($attr['href'])) {
            if ('label' === $style) {
                $v .= '^';
            }
            $target = isset($attr['file']) ? '' : ' target="_blank"';
            $v = \sprintf('<a href="%s"%s rel="noopener noreferrer">%s</a>', esc($this->utf8Encode($attr['href'])), $target, $v);
        }
        if (isset($attr['lang'])) {
            $v = \sprintf('<code class="%s">%s</code>', esc($attr['lang']), $v);
        }
        if ('label' === $style) {
            $v .= ' ';
        }
        if ($attr['virtual'] ?? false) {
            $v = '<span class=sf-dump-virtual>'.$v.'</span>';
        }

        return $v;
    }

    protected function dumpLine(int $depth, bool $endOfValue = false): void
    {
        if (-1 === $this->lastDepth) {
            $this->line = \sprintf($this->dumpPrefix, $this->dumpId, $this->indentPad).$this->line;
        }
        if ($this->headerIsDumped !== ($this->outputStream ?? $this->lineDumper)) {
            $this->line = $this->getDumpHeader().$this->line;
        }

        if (-1 === $depth) {
            $args = ['"'.$this->dumpId.'"'];
            if ($this->extraDisplayOptions) {
                $args[] = json_encode($this->extraDisplayOptions, \JSON_FORCE_OBJECT);
            }
            // Replace is for BC
            $this->line .= \sprintf(str_replace('"%s"', '%s', $this->dumpSuffix), implode(', ', $args));
        }
        $this->lastDepth = $depth;

        $this->line = mb_encode_numericentity($this->line, [0x80, 0x10FFFF, 0, 0x1FFFFF], 'UTF-8');

        if (-1 === $depth) {
            AbstractDumper::dumpLine(0);
        }
        AbstractDumper::dumpLine($depth);
    }

    private function getSourceLink(string $file, int $line): string|false
    {
        $options = $this->extraDisplayOptions + $this->displayOptions;

        if ($fmt = $options['fileLinkFormat']) {
            return \is_string($fmt) ? strtr($fmt, ['%f' => $file, '%l' => $line]) : $fmt->format($file, $line);
        }

        return false;
    }
}

function esc(string $str): string
{
    return htmlspecialchars($str, \ENT_QUOTES, 'UTF-8');
}
