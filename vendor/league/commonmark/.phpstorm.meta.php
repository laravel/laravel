<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPSTORM_META
{
    expectedArguments(\League\CommonMark\Util\HtmlElement::__construct(), 0, 'a', 'abbr', 'address', 'area', 'article', 'aside', 'audio', 'b', 'base', 'bdi', 'bdo', 'blockquote', 'body', 'br', 'button', 'canvas', 'caption', 'cite', 'code', 'col', 'colgroup', 'data', 'datalist', 'dd', 'del', 'details', 'dfn', 'dialog', 'div', 'dl', 'dt', 'em', 'embed', 'fieldset', 'figure', 'footer', 'form', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'head', 'header', 'hgroup', 'hr', 'html', 'i', 'iframe', 'img', 'input', 'ins', 'kdb', 'keygen', 'label', 'legend', 'li', 'link', 'main', 'map', 'mark', 'menu', 'menuitem', 'meta', 'meter', 'nav', 'noscript', 'object', 'ol', 'optgroup', 'option', 'output', 'p', 'param', 'pre', 'progress', 'q', 's', 'samp', 'script', 'section', 'select', 'small', 'source', 'span', 'strong', 'style', 'sub', 'summary', 'sup', 'table', 'tbody', 'td', 'template', 'textarea', 'tfoot', 'th', 'thead', 'time', 'tr', 'track', 'u', 'ul', 'var', 'video', 'wbr');

    expectedArguments(\League\CommonMark\Extension\CommonMark\Node\Block\Heading::__construct(), 0, 1, 2, 3, 4, 5, 6);
    expectedReturnValues(\League\CommonMark\Extension\CommonMark\Node\Block\Heading::getLevel(), 1, 2, 3, 4, 5, 6);

    registerArgumentsSet('league_commonmark_htmlblock_types', \League\CommonMark\Extension\CommonMark\Node\Block\HtmlBlock::TYPE_1_CODE_CONTAINER, \League\CommonMark\Extension\CommonMark\Node\Block\HtmlBlock::TYPE_2_COMMENT, \League\CommonMark\Extension\CommonMark\Node\Block\HtmlBlock::TYPE_3, \League\CommonMark\Extension\CommonMark\Node\Block\HtmlBlock::TYPE_4, \League\CommonMark\Extension\CommonMark\Node\Block\HtmlBlock::TYPE_5_CDATA, \League\CommonMark\Extension\CommonMark\Node\Block\HtmlBlock::TYPE_6_BLOCK_ELEMENT, \League\CommonMark\Extension\CommonMark\Node\Block\HtmlBlock::TYPE_7_MISC_ELEMENT);
    expectedArguments(\League\CommonMark\Extension\CommonMark\Node\Block\HtmlBlock::__construct(), 0, argumentsSet('league_commonmark_htmlblock_types'));
    expectedArguments(\League\CommonMark\Extension\CommonMark\Node\Block\HtmlBlock::setType(), 0, argumentsSet('league_commonmark_htmlblock_types'));
    expectedReturnValues(\League\CommonMark\Extension\CommonMark\Node\Block\HtmlBlock::getType(), argumentsSet('league_commonmark_htmlblock_types'));
    expectedArguments(\League\CommonMark\Util\RegexHelper::getHtmlBlockOpenRegex(), 0, argumentsSet('league_commonmark_htmlblock_types'));
    expectedArguments(\League\CommonMark\Util\RegexHelper::getHtmlBlockCloseRegex(), 0, argumentsSet('league_commonmark_htmlblock_types'));

    registerArgumentsSet('league_commonmark_newline_types', \League\CommonMark\Node\Inline\Newline::HARDBREAK, \League\CommonMark\Node\Inline\Newline::SOFTBREAK);
    expectedArguments(\League\CommonMark\Node\Inline\Newline::__construct(), 0, argumentsSet('league_commonmark_newline_types'));
    expectedReturnValues(\League\CommonMark\Node\Inline\Newline::getType(), argumentsSet('league_commonmark_newline_types'));

    registerArgumentsSet('league_commonmark_options',
        'html_input',
        'allow_unsafe_links',
        'max_nesting_level',
        'max_delimiters_per_line',
        'renderer',
        'renderer/block_separator',
        'renderer/inner_separator',
        'renderer/soft_break',
        'commonmark',
        'commonmark/enable_em',
        'commonmark/enable_strong',
        'commonmark/use_asterisk',
        'commonmark/use_underscore',
        'commonmark/unordered_list_markers',
        'disallowed_raw_html',
        'disallowed_raw_html/disallowed_tags',
        'external_link',
        'external_link/html_class',
        'external_link/internal_hosts',
        'external_link/nofollow',
        'external_link/noopener',
        'external_link/noreferrer',
        'external_link/open_in_new_window',
        'footnote',
        'footnote/backref_class',
        'footnote/backref_symbol',
        'footnote/container_add_hr',
        'footnote/container_class',
        'footnote/ref_class',
        'footnote/ref_id_prefix',
        'footnote/footnote_class',
        'footnote/footnote_id_prefix',
        'heading_permalink',
        'heading_permalink/apply_id_to_heading',
        'heading_permalink/heading_class',
        'heading_permalink/html_class',
        'heading_permalink/fragment_prefix',
        'heading_permalink/id_prefix',
        'heading_permalink/inner_contents',
        'heading_permalink/insert',
        'heading_permalink/max_heading_level',
        'heading_permalink/min_heading_level',
        'heading_permalink/symbol',
        'heading_permalink/title',
        'mentions',
        'smartpunct/double_quote_closer',
        'smartpunct/double_quote_opener',
        'smartpunct/single_quote_closer',
        'smartpunct/single_quote_opener',
        'slug_normalizer',
        'slug_normalizer/instance',
        'slug_normalizer/max_length',
        'slug_normalizer/unique',
        'table',
        'table/wrap',
        'table/wrap/attributes',
        'table/wrap/enabled',
        'table/wrap/tag',
        'table/alignment_attributes',
        'table/alignment_attributes/left',
        'table/alignment_attributes/center',
        'table/alignment_attributes/right',
        'table/max_autocompleted_cells',
        'table_of_contents',
        'table_of_contents/html_class',
        'table_of_contents/max_heading_level',
        'table_of_contents/min_heading_level',
        'table_of_contents/normalize',
        'table_of_contents/placeholder',
        'table_of_contents/position',
        'table_of_contents/style',
    );
    expectedArguments(\League\Config\ConfigurationInterface::get(), 0, argumentsSet('league_commonmark_options'));
    expectedArguments(\League\Config\ConfigurationInterface::exists(), 0, argumentsSet('league_commonmark_options'));
    expectedArguments(\League\Config\MutableConfigurationInterface::set(), 0, argumentsSet('league_commonmark_options'));
}
