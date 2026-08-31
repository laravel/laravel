import Alpine from 'alpinejs';
import tippy from 'tippy.js';
import { createHighlighterCoreSync } from 'shiki/core';
import { createJavaScriptRegexEngine } from 'shiki/engine/javascript';
import json from '@shikijs/langs/json';
import php from '@shikijs/langs/php';
import sql from '@shikijs/langs/sql';
import darkPlus from '@shikijs/themes/dark-plus';
import lightPlus from '@shikijs/themes/light-plus';

tippy('[data-tippy-content]', {
    arrow: false,
    allowHTML: true,
    animation: 'shift-away',
    delay: [300, 0],
    duration: 200,
    theme: 'laravel',
});

window.copyToClipboard = async function (text) {
    if (navigator.clipboard) {
        await navigator.clipboard.writeText(text);
    } else {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        textarea.style.pointerEvents = 'none';
        document.body.appendChild(textarea);
        textarea.select();

        const result = document.execCommand('copy');
        document.body.removeChild(textarea);

        if (!result) {
            throw new Error('Failed to copy text to clipboard');
        }
    }
};

const highlighter = createHighlighterCoreSync({
    themes: [lightPlus, darkPlus],
    langs: [php, sql, json],
    engine: createJavaScriptRegexEngine(),
});

window.highlight = function (
    code,
    language,
    truncate = false,
    editor = false,
    startingLine = 1,
    highlightedLine = null
) {
    return highlighter.codeToHtml(code, {
        lang: language,
        themes: {
            light: 'light-plus',
            dark: 'dark-plus',
        },
        transformers: [
            {
                pre(node) {
                    this.addClassToHast(node, ['bg-transparent!', truncate ? 'truncate' : 'w-fit min-w-full']);
                },
                line(node, line) {
                    if (!editor) {
                        return;
                    }

                    const lineNumber = startingLine + line - 1;
                    const highlight = highlightedLine === line - 1;

                    const lineNumberSpan = {
                        type: 'element',
                        tagName: 'span',
                        properties: {
                            className: [
                                'mr-6 text-neutral-500! dark:text-neutral-600!',
                                highlight ? 'dark:text-white!' : '',
                            ],
                        },
                        children: [{ type: 'text', value: lineNumber.toString() }],
                    };

                    node.children.unshift(lineNumberSpan);

                    this.addClassToHast(node, [
                        'inline-block w-full px-4 py-1 h-7 even:bg-white odd:bg-white/2 even:dark:bg-white/2 odd:dark:bg-white/4',
                        highlight ? 'bg-rose-200! dark:bg-rose-900!' : '',
                    ]);
                },
            },
        ],
    });
};

window.Alpine = Alpine;

Alpine.start();
