<?php
/**
 *  Base include file for SimpleTest.
 *  @package    SimpleTest
 *  @subpackage WebTester
 *  @version    $Id: tag.php 1788 2008-04-27 11:01:59Z pp11 $
 */
    
/**#@+
 * include SimpleTest files
 */
require_once(dirname(__FILE__) . '/parser.php');
require_once(dirname(__FILE__) . '/encoding.php');
/**#@-*/

/**
 *    HTML or XML tag.
 *    @package SimpleTest
 *    @subpackage WebTester
 */
class SimpleTag {
    private $name;
    private $attributes;
    private $content;
    
    /**
     *    Starts with a named tag with attributes only.
     *    @param string $name        Tag name.
     *    @param hash $attributes    Attribute names and
     *                               string values. Note that
     *                               the keys must have been
     *                               converted to lower case.
     */
    function __construct($name, $attributes) {
        $this->name = strtolower(trim($name));
        $this->attributes = $attributes;
        $this->content = '';
    }
    
    /**
     *    Check to see if the tag can have both start and
     *    end tags with content in between.
     *    @return boolean        True if content allowed.
     *    @access public
     */
    function expectEndTag() {
        return true;
    }
    
    /**
     *    The current tag should not swallow all content for
     *    itself as it's searchable page content. Private
     *    content tags are usually widgets that contain default
     *    values.
     *    @return boolean        False as content is available
     *                           to other tags by default.
     *    @access public
     */
    function isPrivateContent() {
        return false;
    }

    /**
     *    Appends string content to the current content.
     *    @param string $content        Additional text.
     *    @access public
     */
    function addContent($content) {
        $this->content .= (string)$content;
    }
    
    /**
     *    Adds an enclosed tag to the content.
     *    @param SimpleTag $tag    New tag.
     *    @access public
     */
    function addTag($tag) {
    }
    
    /**
     *    Accessor for tag name.
     *    @return string       Name of tag.
     *    @access public
     */
    function getTagName() {
        return $this->name;
    }
    
    /**
     *    List of legal child elements.
     *    @return array        List of element names.
     *    @access public
     */
    function getChildElements() {
        return array();
    }
    
    /**
     *    Accessor for an attribute.
     *    @param string $label    Attribute name.
     *    @return string          Attribute value.
     *    @access public
     */
    function getAttribute($label) {
        $label = strtolower($label);
        if (! isset($this->attributes[$label])) {
            return false;
        }
        return (string)$this->attributes[$label];
    }
    
    /**
     *    Sets an attribute.
     *    @param string $label    Attribute name.
     *    @return string $value   New attribute value.
     *    @access protected
     */
    protected function setAttribute($label, $value) {
        $this->attributes[strtolower($label)] = $value;
    }
    
    /**
     *    Accessor for the whole content so far.
     *    @return string       Content as big raw string.
     *    @access public
     */
    function getContent() {
        return $this->content;
    }
    
    /**
     *    Accessor for content reduced to visible text. Acts
     *    like a text mode browser, normalising space and
     *    reducing images to their alt text.
     *    @return string       Content as plain text.
     *    @access public
     */
    function getText() {
        return SimpleHtmlSaxParser::normalise($this->content);
    }
    
    /**
     *    Test to see if id attribute matches.
     *    @param string $id        ID to test against.
     *    @return boolean          True on match.
     *    @access public
     */
    function isId($id) {
        return ($this->getAttribute('id') == $id);
    }
}

/**
 *    Base url.
 *    @package SimpleTest
 *    @subpackage WebTester
 */
class SimpleBaseTag extends SimpleTag {
    
    /**
     *    Starts with a named tag with attributes only.
     *    @param hash $attributes    Attribute names and
     *                               string values.
     */
    function __construct($attributes) {
        parent::__construct('base', $attributes);
    }

    /**
     *    Base tag is not a block tag.
     *    @return boolean       false
     *    @access public
     */
    function expectEndTag() {
        return false;
    }
}

/**
 *    Page title.
 *    @package SimpleTest
 *    @subpackage WebTester
 */
class SimpleTitleTag extends SimpleTag {
    
    /**
     *    Starts with a named tag with attributes only.
     *    @param hash $attributes    Attribute names and
     *                               string values.
     */
    function __construct($attributes) {
        parent::__construct('title', $attributes);
    }
}

/**
 *    Link.
 *    @package SimpleTest
 *    @subpackage WebTester
 */
class SimpleAnchorTag extends SimpleTag {
    
    /**
     *    Starts with a named tag with attributes only.
     *    @param hash $attributes    Attribute names and
     *                               string values.
     */
    function __construct($attributes) {
        parent::__construct('a', $attributes);
    }
    
    /**
     *    Accessor for URL as string.
     *    @return string    Coerced as string.
     *    @access public
     */
    function getHref() {
        $url = $this->getAttribute('href');
        if (is_bool($url)) {
            $url = '';
        }
        return $url;
    }
}

/**
 *    Form element.
 *    @package SimpleTest
 *    @subpackage WebTester
 */
class SimpleWidget extends SimpleTag {
    private $value;
    private $label;
    private $is_set;
    
    /**
     *    Starts with a named tag with attributes only.
     *    @param string $name        Tag name.
     *    @param hash $attributes    Attribute names and
     *                               string values.
     */
    function __construct($name, $attributes) {
        parent::__construct($name, $attributes);
        $this->value = false;
        $this->label = false;
        $this->is_set = false;
    }
    
    /**
     *    Accessor for name submitted as the key in
     *    GET/POST privateiables hash.
     *    @return string        Parsed value.
     *    @access public
     */
    function getName() {
        return $this->getAttribute('name');
    }
    
    /**
     *    Accessor for default value parsed with the tag.
     *    @return string        Parsed value.
     *    @access public
     */
    function getDefault() {
        return $this->getAttribute('value');
    }
    
    /**
     *    Accessor for currently set value or default if
     *    none.
     *    @return string      Value set by form or default
     *                        if none.
     *    @access public
     */
    function getValue() {
        if (! $this->is_set) {
            return $this->getDefault();
        }
        return $this->value;
    }
    
    /**
     *    Sets the current form element value.
     *    @param string $value       New value.
     *    @return boolean            True if allowed.
     *    @access public
     */
    function setValue($value) {
        $this->value = $value;
        $this->is_set = true;
        return true;
    }
    
    /**
     *    Resets the form element value back to the
     *    default.
     *    @access public
     */
    function resetValue() {
        $this->is_set = false;
    }
    
    /**
     *    Allows setting of a label externally, say by a
     *    label tag.
     *    @param string $label    Label to attach.
     *    @access public
     */
    function setLabel($label) {
        $this->label = trim($label);
    }
    
    /**
     *    Reads external or internal label.
     *    @param string $label    Label to test.
     *    @return boolean         True is match.
     *    @access public
     */
    function isLabel($label) {
        return $this->label == trim($label);
    }
    
    /**
     *    Dispatches the value into the form encoded packet.
     *    @param SimpleEncoding $encoding    Form packet.
     *    @access public
     */
    function write($encoding) {
        if ($this->getName()) {
            $encoding->add($this->getName(), $this->getValue());
        }
    }
}

/**
 *    Text, password and hidden field.
 *    @package SimpleTest
 *    @subpackage WebTester
 */
class SimpleTextTag extends SimpleWidget {
    
    /**
     *    Starts with a named tag with attributes only.
     *    @param hash $attributes    Attribute names and
     *                               string values.
     */
    function __construct($attributes) {
        parent::__construct('input', $attributes);
        if ($this->getAttribute('value') === false) {
            $this->setAttribute('value', '');
        }
    }
    
    /**
     *    Tag contains no content.
     *    @return boolean        False.
     *    @access public
     */
    function expectEndTag() {
        return false;
    }
    
    /**
     *    Sets the current form element value. Cannot
     *    change the value of a hidden field.
     *    @param string $value       New value.
     *    @return boolean            True if allowed.
     *    @access public
     */
    function setValue($value) {
        if ($this->getAttribute('type') == 'hidden') {
            return false;
        }
        return parent::setValue($value);
    }
}

/**
 *    Submit button as input tag.
 *    @package SimpleTest
 *    @subpackage WebTester
 */
class SimpleSubmitTag extends SimpleWidget {
    
    /**
     *    Starts with a named tag with attributes only.
     *    @param hash $attributes    Attribute names and
     *                               string values.
     */
    function __construct($attributes) {
        parent::__construct('input', $attributes);
        if ($this->getAttribute('value') === false) {
            $this->setAttribute('value', 'Submit');
        }
    }
    
    /**
     *    Tag contains no end element.
     *    @return boolean        False.
     *    @access public
     */
    function expectEndTag() {
        return false;
    }
    
    /**
     *    Disables the setting of the button value.
     *    @param string $value       Ignored.
     *    @return boolean            True if allowed.
     *    @access public
     */
    function setValue($value) {
        return false;
    }
    
    /**
     *    Value of browser visible text.
     *    @return string        Visible label.
     *    @access public
     */
    function getLabel() {
        return $this->getValue();
    }
    
    /**
     *    Test for a label match when searching.
     *    @param string $label     Label to test.
     *    @return boolean          True on match.
     *    @access public
     */
    function isLabel($label) {
        return trim($label) == trim($this->getLabel());
    }
}
    
/**
 *    Image button as input tag.
 *    @package SimpleTest
 *    @subpackage WebTester
 */
class SimpleImageSubmitTag extends SimpleWidget {
    
    /**
     *    Starts with a named tag with attributes only.
     *    @param hash $attributes    Attribute names and
     *                               string values.
     */
    function __construct($attributes) {
        parent::__construct('input', $attributes);
    }
    
    /**
     *    Tag contains no end element.
     *    @return boolean        False.
     *    @access public
     */
    function expectEndTag() {
        return false;
    }
    
    /**
     *    Disables the setting of the button value.
     *    @param string $value       Ignored.
     *    @return boolean            True if allowed.
     *    @access public
     */
    function setValue($value) {
        return false;
    }
    
    /**
     *    Value of browser visible text.
     *    @return string        Visible label.
     *    @access public
     */
    function getLabel() {
        if ($this->getAttribute('title')) {
            return $this->getAttribute('title');
        }
        return $this->getAttribute('alt');
    }
    
    /**
     *    Test for a label match when searching.
     *    @param string $label     Label to test.
     *    @return boolean          True on match.
     *    @access public
     */
    function isLabel($label) {
        return trim($label) == trim($this->getLabel());
    }
    
    /**
     *    Dispatches the value into the form encoded packet.
     *    @param SimpleEncoding $encoding    Form packet.
     *    @param integer $x                  X coordinate of click.
     *    @param integer $y                  Y coordinate of click.
     *    @access public
     */
    function write($encoding, $x = 1, $y = 1) {
        if ($this->getName()) {
            $encoding->add($this->getName() . '.x', $x);
            $encoding->add($this->getName() . '.y', $y);
        } else {
            $encoding->add('x', $x);
            $encoding->add('y', $y);
        }
    }
}
    
/**
 *    Submit button as button tag.
 *    @package SimpleTest
 *    @subpackage WebTester
 */
class SimpleButtonTag extends SimpleWidget {
    
    /**
     *    Starts with a named tag with attributes only.
     *    Defaults are very browser dependent.
     *    @param hash $attributes    Attribute names and
     *                               string values.
     */
    function __construct($attributes) {
        parent::__construct('button', $attributes);
    }
    
    /**
     *    Check to see if the tag can have both start and
     *    end tags with content in between.
     *    @return boolean        True if content allowed.
     *    @access public
     */
    function expectEndTag() {
        return true;
    }
    
    /**
     *    Disables the setting of the button value.
     *    @param string $value       Ignored.
     *    @return boolean            True if allowed.
     *    @access public
     */
    function setValue($value) {
        return false;
    }
    
    /**
     *    Value of browser visible text.
     *    @return string        Visible label.
     *    @access public
     */
    function getLabel() {
        return $this->getContent();
    }
    
    /**
     *    Test for a label match when searching.
     *    @param string $label     Label to test.
     *    @return boolean          True on match.
     *    @access public
     */
    function isLabel($label) {
        return trim($label) == trim($this->getLabel());
    }
}

/**
 *    Content tag for text area.
 *    @package SimpleTest
 *    @subpackage WebTester
 */
class SimpleTextAreaTag extends SimpleWidget {
    
    /**
     *    Starts with a named tag with attributes only.
     *    @param hash $attributes    Attribute names and
     *                               string values.
     */
    function __construct($attributes) {
        parent::__construct('textarea', $attributes);
    }
    
    /**
     *    Accessor for starting value.
     *    @return string        Parsed value.
     *    @access public
     */
    function getDefault() {
        return $this->wrap(SimpleHtmlSaxParser::decodeHtml($this->getContent()));
    }
    
    /**
     *    Applies word wrapping if needed.
     *    @param string $value      New value.
     *    @return boolean            True if allowed.
     *    @access public
     */
    function setValue($value) {
        return parent::setValue($this->wrap($value));
    }
    
    /**
     *    Test to see if text should be wrapped.
     *    @return boolean        True if wrapping on.
     *    @access private
     */
    function wrapIsEnabled() {
        if ($this->getAttribute('cols')) {
            $wrap = $this->getAttribute('wrap');
            if (($wrap == 'physical') || ($wrap == 'hard')) {
                return true;
            }
        }
        return false;
    }
    
    /**
     *    Performs the formatting that is peculiar to
     *    this tag. There is strange behaviour in this
     *    one, including stripping a leading new line.
     *    Go figure. I am using Firefox as a guide.
     *    @param string $text    Text to wrap.
     *    @return string         Text wrapped with carriage
     *                           returns and line feeds
     *    @access private
     */
    protected function wrap($text) {
        $text = str_replace("\r\r\n", "\r\n", str_replace("\n", "\r\n", $text));
        $text = str_replace("\r\n\n", "\r\n", str_replace("\r", "\r\n", $text));
        if (strncmp($text, "\r\n", strlen("\r\n")) == 0) {
            $text = substr($text, strlen("\r\n"));
        }
        if ($this->wrapIsEnabled()) {
            return wordwrap(
                    $text,
                    (integer)$this->getAttribute('cols'),
                    "\r\n");
        }
        return $text;
    }
    
    /**
     *    The content of textarea is not part of the page.
     *    @return boolean        True.
     *    @access public
     */
    function isPrivateContent() {
        return true;
    }
}

/**
 *    File upload widget.
 *    @package SimpleTest
 *    @subpackage WebTester
 */
class SimpleUploadTag extends SimpleWidget {
    
    /**
     *    Starts with attributes only.
     *    @param hash $attributes    Attribute names and
     *                               string values.
     */
    function __construct($attributes) {
        parent::__construct('input', $attributes);
    }
    
    /**
     *    Tag contains no content.
     *    @return boolean        False.
     *    @access public
     */
    function expectEndTag() {
        return false;
    }
    
    /**
     *    Dispatches the value into the form encoded packet.
     *    @param SimpleEncoding $encoding    Form packet.
     *    @access public
     */
    function write($encoding) {
        if (! file_exists($this->getValue())) {
            return;
        }
        $encoding->attach(
                $this->getName(),
                implode('', file($this->getValue())),
                basename($this->getValue()));
    }
}

/**
 *    Drop down widget.
 *    @package SimpleTest
 *    @subpackage WebTester
 */
class SimpleSelectionTag extends SimpleWidget {
    private $options;
    private $choice;
    
    /**
     *    Starts with attributes only.
     *    @param hash $attributes    Attribute names and
     *                               string values.
     */
    function __construct($attributes) {
        parent::__construct('select', $attributes);
        $this->options = array();
        $this->choice = false;
    }
    
    /**
     *    Adds an option tag to a selection field.
     *    @param SimpleOptionTag $tag     New option.
     *    @access public
     */
    function addTag($tag) {
        if ($tag->getTagName() == 'option') {
            $this->options[] = $tag;
        }
    }
    
    /**
     *    Text within the selection element is ignored.
     *    @param string $content        Ignored.
     *    @access public
     */
    function addContent($content) {
    }
    
    /**
     *    Scans options for defaults. If none, then
     *    the first option is selected.
     *    @return string        Selected field.
     *    @access public
     */
    function getDefault() {
        for ($i = 0, $count = count($this->options); $i < $count; $i++) {
            if ($this->options[$i]->getAttribute('selected') !== false) {
                return $this->options[$i]->getDefault();
            }
        }
        if ($count > 0) {
            return $this->options[0]->getDefault();
        }
        return '';
    }
    
    /**
     *    Can only set allowed values.
     *    @param string $value       New choice.
     *    @return boolean            True if allowed.
     *    @access public
     */
    function setValue($value) {
        for ($i = 0, $count = count($this->options); $i < $count; $i++) {
            if ($this->options[$i]->isValue($value)) {
                $this->choice = $i;
                return true;
            }
        }
        return false;
    }
    
    /**
     *    Accessor for current selection value.
     *    @return string      Value attribute or
     *                        content of opton.
     *    @access public
     */
    function getValue() {
        if ($this->choice === false) {
            return $this->getDefault();
        }
        return $this->options[$this->choice]->getValue();
    }
}

/**
 *    Drop down widget.
 *    @package SimpleTest
 *    @subpackage WebTester
 */
class MultipleSelectionTag extends SimpleWidget {
    private $options;
    private $values;
    
    /**
     *    Starts with attributes only.
     *    @param hash $attributes    Attribute names and
     *                               string values.
     */
    function __construct($attributes) {
        parent::__construct('select', $attributes);
        $this->options = array();
        $this->values = false;
    }
    
    /**
     *    Adds an option tag to a selection field.
     *    @param SimpleOptionTag $tag     New option.
     *    @access public
     */
    function addTag($tag) {
        if ($tag->getTagName() == 'option') {
            $this->options[] = &$tag;
        }
    }
    
    /**
     *    Text within the selection element is ignored.
     *    @param string $content        Ignored.
     *    @access public
     */
    function addContent($content) {
    }
    
    /**
     *    Scans options for defaults to populate the
     *    value array().
     *    @return array        Selected fields.
     *    @access public
     */
    function getDefault() {
        $default = array();
        for ($i = 0, $count = count($this->options); $i < $count; $i++) {
            if ($this->options[$i]->getAttribute('selected') !== false) {
                $default[] = $this->options[$i]->getDefault();
            }
        }
        return $default;
    }
    
    /**
     *    Can only set allowed values. Any illegal value
     *    will result in a failure, but all correct values
     *    will be set.
     *    @param array $desired      New choices.
     *    @return boolean            True if all allowed.
     *    @access public
     */
    function setValue($desired) {
        $achieved = array();
        foreach ($desired as $value) {
            $success = false;
            for ($i = 0, $count = count($this->options); $i < $count; $i++) {
                if ($this->options[$i]->isValue($value)) {
                    $achieved[] = $this->options[$i]->getValue();
                    $success = true;
                    break;
                }
            }
            if (! $success) {
                return false;
            }
        }
        $this->values = $achieved;
        return true;
    }
    
    /**
     *    Accessor for current selection value.
     *    @return array      List of currently set options.
     *    @access public
     */
    function getValue() {
        if ($this->values === false) {
            return $this->getDefault();
        }
        return $this->values;
    }
}

/**
 *    Option for selection field.
 *    @package SimpleTest
 *    @subpackage WebTester
 */
class SimpleOptionTag extends SimpleWidget {
    
    /**
     *    Stashes the attributes.
     */
    function __construct($attributes) {
        parent::__construct('option', $attributes);
    }
    
    /**
     *    Does nothing.
     *    @param string $value      Ignored.
     *    @return boolean           Not allowed.
     *    @access public
     */
    function setValue($value) {
        return false;
    }
    
    /**
     *    Test to see if a value matches the option.
     *    @param string $compare    Value to compare with.
     *    @return boolean           True if possible match.
     *    @access public
     */
    function isValue($compare) {
        $compare = trim($compare);
        if (trim($this->getValue()) == $compare) {
            return true;
        }
        return trim($this->getContent()) == $compare;
    }
    
    /**
     *    Accessor for starting value. Will be set to
     *    the option label if no value exists.
     *    @return string        Parsed value.
     *    @access public
     */
    function getDefault() {
        if ($this->getAttribute('value') === false) {
            return $this->getContent();
        }
        return $this->getAttribute('value');
    }
    
    /**
     *    The content of options is not part of the page.
     *    @return boolean        True.
     *    @access public
     */
    function isPrivateContent() {
        return true;
    }
}

/**
 *    Radio button.
 *    @package SimpleTest
 *    @subpackage WebTester
 */
class SimpleRadioButtonTag extends SimpleWidget {
    
    /**
     *    Stashes the attributes.
     *    @param array $attributes        Hash of attributes.
     */
    function __construct($attributes) {
        parent::__construct('input', $attributes);
        if ($this->getAttribute('value') === false) {
            $this->setAttribute('value', 'on');
        }
    }
    
    /**
     *    Tag contains no content.
     *    @return boolean        False.
     *    @access public
     */
    function expectEndTag() {
        return false;
    }
    
    /**
     *    The only allowed value sn the one in the
     *    "value" attribute.
     *    @param string $value      New value.
     *    @return boolean           True if allowed.
     *    @access public
     */
    function setValue($value) {
        if ($value === false) {
            return parent::setValue($value);
        }
        if ($value != $this->getAttribute('value')) {
            return false;
        }
        return parent::setValue($value);
    }
    
    /**
     *    Accessor for starting value.
     *    @return string        Parsed value.
     *    @access public
     */
    function getDefault() {
        if ($this->getAttribute('checked') !== false) {
            return $this->getAttribute('value');
        }
        return false;
    }
}

/**
 *    Checkbox widget.
 *    @package SimpleTest
 *    @subpackage WebTester
 */
class SimpleCheckboxTag extends SimpleWidget {
    
    /**
     *    Starts with attributes only.
     *    @param hash $attributes    Attribute names and
     *                               string values.
     */
    function __construct($attributes) {
        parent::__construct('input', $attributes);
        if ($this->getAttribute('value') === false) {
            $this->setAttribute('value', 'on');
        }
    }
    
    /**
     *    Tag contains no content.
     *    @return boolean        False.
     *    @access public
     */
    function expectEndTag() {
        return false;
    }
    
    /**
     *    The only allowed value in the one in the
     *    "value" attribute. The default for this
     *    attribute is "on". If this widget is set to
     *    true, then the usual value will be taken.
     *    @param string $value      New value.
     *    @return boolean           True if allowed.
     *    @access public
     */
    function setValue($value) {
        if ($value === false) {
            return parent::setValue($value);
        }
        if ($value === true) {
            return parent::setValue($this->getAttribute('value'));
        }
        if ($value != $this->getAttribute('value')) {
            return false;
        }
        return parent::setValue($value);
    }
    
    /**
     *    Accessor for starting value. The default
     *    value is "on".
     *    @return string        Parsed value.
     *    @access public
     */
    function getDefault() {
        if ($this->getAttribute('checked') !== false) {
            return $this->getAttribute('value');
        }
        return false;
    }
}

/**
 *    A group of multiple widgets with some shared behaviour.
 *    @package SimpleTest
 *    @subpackage WebTester
 */
class SimpleTagGroup {
    private $widgets = array();

    /**
     *    Adds a tag to the group.
     *    @param SimpleWidget $widget
     *    @access public
     */
    function addWidget($widget) {
        $this->widgets[] = $widget;
    }
    
    /**
     *    Accessor to widget set.
     *    @return array        All widgets.
     *    @access protected
     */
    protected function &getWidgets() {
        return $this->widgets;
    }

    /**
     *    Accessor for an attribute.
     *    @param string $label    Attribute name.
     *    @return boolean         Always false.
     *    @access public
     */
    function getAttribute($label) {
        return false;
    }
    
    /**
     *    Fetches the name for the widget from the first
     *    member.
     *    @return string        Name of widget.
     *    @access public
     */
    function getName() {
        if (count($this->widgets) > 0) {
            return $this->widgets[0]->getName();
        }
    }
    
    /**
     *    Scans the widgets for one with the appropriate
     *    ID field.
     *    @param string $id        ID value to try.
     *    @return boolean          True if matched.
     *    @access public
     */
    function isId($id) {
        for ($i = 0, $count = count($this->widgets); $i < $count; $i++) {
            if ($this->widgets[$i]->isId($id)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     *    Scans the widgets for one with the appropriate
     *    attached label.
     *    @param string $label     Attached label to try.
     *    @return boolean          True if matched.
     *    @access public
     */
    function isLabel($label) {
        for ($i = 0, $count = count($this->widgets); $i < $count; $i++) {
            if ($this->widgets[$i]->isLabel($label)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     *    Dispatches the value into the form encoded packet.
     *    @param SimpleEncoding $encoding    Form packet.
     *    @access public
     */
    function write($encoding) {
        $encoding->add($this->getName(), $this->getValue());
    }
}

/**
 *    A group of tags with the same name within a form.
 *    @package SimpleTest
 *    @subpackage WebTester
 */
class SimpleCheckboxGroup extends SimpleTagGroup {
    
    /**
     *    Accessor for current selected widget or false
     *    if none.
     *    @return string/array     Widget values or false if none.
     *    @access public
     */
    function getValue() {
        $values = array();
        $widgets = $this->getWidgets();
        for ($i = 0, $count = count($widgets); $i < $count; $i++) {
            if ($widgets[$i]->getValue() !== false) {
                $values[] = $widgets[$i]->getValue();
            }
        }
        return $this->coerceValues($values);
    }
    
    /**
     *    Accessor for starting value that is active.
     *    @return string/array      Widget values or false if none.
     *    @access public
     */
    function getDefault() {
        $values = array();
        $widgets = $this->getWidgets();
        for ($i = 0, $count = count($widgets); $i < $count; $i++) {
            if ($widgets[$i]->getDefault() !== false) {
                $values[] = $widgets[$i]->getDefault();
            }
        }
        return $this->coerceValues($values);
    }
    
    /**
     *    Accessor for current set values.
     *    @param string/array/boolean $values   Either a single string, a
     *                                          hash or false for nothing set.
     *    @return boolean                       True if all values can be set.
     *    @access public
     */
    function setValue($values) {
        $values = $this->makeArray($values);
        if (! $this->valuesArePossible($values)) {
            return false;
        }
        $widgets = $this->getWidgets();
        for ($i = 0, $count = count($widgets); $i < $count; $i++) {
            $possible = $widgets[$i]->getAttribute('value');
            if (in_array($widgets[$i]->getAttribute('value'), $values)) {
                $widgets[$i]->setValue($possible);
            } else {
                $widgets[$i]->setValue(false);
            }
        }
        return true;
    }
    
    /**
     *    Tests to see if a possible value set is legal.
     *    @param string/array/boolean $values   Either a single string, a
     *                                          hash or false for nothing set.
     *    @return boolean                       False if trying to set a
     *                                          missing value.
     *    @access private
     */
    protected function valuesArePossible($values) {
        $matches = array();
        $widgets = &$this->getWidgets();
        for ($i = 0, $count = count($widgets); $i < $count; $i++) {
            $possible = $widgets[$i]->getAttribute('value');
            if (in_array($possible, $values)) {
                $matches[] = $possible;
            }
        }
        return ($values == $matches);
    }
    
    /**
     *    Converts the output to an appropriate format. This means
     *    that no values is false, a single value is just that
     *    value and only two or more are contained in an array.
     *    @param array $values           List of values of widgets.
     *    @return string/array/boolean   Expected format for a tag.
     *    @access private
     */
    protected function coerceValues($values) {
        if (count($values) == 0) {
            return false;
        } elseif (count($values) == 1) {
            return $values[0];
        } else {
            return $values;
        }
    }
    
    /**
     *    Converts false or string into array. The opposite of
     *    the coercian method.
     *    @param string/array/boolean $value  A single item is converted
     *                                        to a one item list. False
     *                                        gives an empty list.
     *    @return array                       List of values, possibly empty.
     *    @access private
     */
    protected function makeArray($value) {
        if ($value === false) {
            return array();
        }
        if (is_string($value)) {
            return array($value);
        }
        return $value;
    }
}

/**
 *    A group of tags with the same name within a form.
 *    Used for radio buttons.
 *    @package SimpleTest
 *    @subpackage WebTester
 */
class SimpleRadioGroup extends SimpleTagGroup {
    
    /**
     *    Each tag is tried in turn until one is
     *    successfully set. The others will be
     *    unchecked if successful.
     *    @param string $value      New value.
     *    @return boolean           True if any allowed.
     *    @access public
     */
    function setValue($value) {
        if (! $this->valueIsPossible($value)) {
            return false;
        }
        $index = false;
        $widgets = $this->getWidgets();
        for ($i = 0, $count = count($widgets); $i < $count; $i++) {
            if (! $widgets[$i]->setValue($value)) {
                $widgets[$i]->setValue(false);
            }
        }
        return true;
    }
    
    /**
     *    Tests to see if a value is allowed.
     *    @param string    Attempted value.
     *    @return boolean  True if a valid value.
     *    @access private
     */
    protected function valueIsPossible($value) {
        $widgets = $this->getWidgets();
        for ($i = 0, $count = count($widgets); $i < $count; $i++) {
            if ($widgets[$i]->getAttribute('value') == $value) {
                return true;
            }
        }
        return false;
    }
    
    /**
     *    Accessor for current selected widget or false
     *    if none.
     *    @return string/boolean   Value attribute or
     *                             content of opton.
     *    @access public
     */
    function getValue() {
        $widgets = $this->getWidgets();
        for ($i = 0, $count = count($widgets); $i < $count; $i++) {
            if ($widgets[$i]->getValue() !== false) {
                return $widgets[$i]->getValue();
            }
        }
        return false;
    }
    
    /**
     *    Accessor for starting value that is active.
     *    @return string/boolean      Value of first checked
     *                                widget or false if none.
     *    @access public
     */
    function getDefault() {
        $widgets = $this->getWidgets();
        for ($i = 0, $count = count($widgets); $i < $count; $i++) {
            if ($widgets[$i]->getDefault() !== false) {
                return $widgets[$i]->getDefault();
            }
        }
        return false;
    }
}

/**
 *    Tag to keep track of labels.
 *    @package SimpleTest
 *    @subpackage WebTester
 */
class SimpleLabelTag extends SimpleTag {
    
    /**
     *    Starts with a named tag with attributes only.
     *    @param hash $attributes    Attribute names and
     *                               string values.
     */
    function __construct($attributes) {
        parent::__construct('label', $attributes);
    }
    
    /**
     *    Access for the ID to attach the label to.
     *    @return string        For attribute.
     *    @access public
     */
    function getFor() {
        return $this->getAttribute('for');
    }
}

/**
 *    Tag to aid parsing the form.
 *    @package SimpleTest
 *    @subpackage WebTester
 */
class SimpleFormTag extends SimpleTag {
    
    /**
     *    Starts with a named tag with attributes only.
     *    @param hash $attributes    Attribute names and
     *                               string values.
     */
    function __construct($attributes) {
        parent::__construct('form', $attributes);
    }
}

/**
 *    Tag to aid parsing the frames in a page.
 *    @package SimpleTest
 *    @subpackage WebTester
 */
class SimpleFrameTag extends SimpleTag {
    
    /**
     *    Starts with a named tag with attributes only.
     *    @param hash $attributes    Attribute names and
     *                               string values.
     */
    function __construct($attributes) {
        parent::__construct('frame', $attributes);
    }
    
    /**
     *    Tag contains no content.
     *    @return boolean        False.
     *    @access public
     */
    function expectEndTag() {
        return false;
    }
}
?>