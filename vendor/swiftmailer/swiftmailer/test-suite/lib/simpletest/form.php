<?php
/**
 *  Base include file for SimpleTest.
 *  @package    SimpleTest
 *  @subpackage WebTester
 *  @version    $Id: form.php 1784 2008-04-26 13:07:14Z pp11 $
 */
    
/**#@+
 * include SimpleTest files
 */
require_once(dirname(__FILE__) . '/tag.php');
require_once(dirname(__FILE__) . '/encoding.php');
require_once(dirname(__FILE__) . '/selector.php');
/**#@-*/

/**
 *    Form tag class to hold widget values.
 *    @package SimpleTest
 *    @subpackage WebTester
 */
class SimpleForm {
    private $method;
    private $action;
    private $encoding;
    private $default_target;
    private $id;
    private $buttons;
    private $images;
    private $widgets;
    private $radios;
    private $checkboxes;
    
    /**
     *    Starts with no held controls/widgets.
     *    @param SimpleTag $tag        Form tag to read.
     *    @param SimplePage $page      Holding page.
     */
    function __construct($tag, $page) {
        $this->method = $tag->getAttribute('method');
        $this->action = $this->createAction($tag->getAttribute('action'), $page);
        $this->encoding = $this->setEncodingClass($tag);
        $this->default_target = false;
        $this->id = $tag->getAttribute('id');
        $this->buttons = array();
        $this->images = array();
        $this->widgets = array();
        $this->radios = array();
        $this->checkboxes = array();
    }
    
    /**
     *    Creates the request packet to be sent by the form.
     *    @param SimpleTag $tag        Form tag to read.
     *    @return string               Packet class.
     *    @access private
     */
    protected function setEncodingClass($tag) {
        if (strtolower($tag->getAttribute('method')) == 'post') {
            if (strtolower($tag->getAttribute('enctype')) == 'multipart/form-data') {
                return 'SimpleMultipartEncoding';
            }
            return 'SimplePostEncoding';
        }
        return 'SimpleGetEncoding';
    }
    
    /**
     *    Sets the frame target within a frameset.
     *    @param string $frame        Name of frame.
     *    @access public
     */
    function setDefaultTarget($frame) {
        $this->default_target = $frame;
    }
    
    /**
     *    Accessor for method of form submission.
     *    @return string           Either get or post.
     *    @access public
     */
    function getMethod() {
        return ($this->method ? strtolower($this->method) : 'get');
    }
    
    /**
     *    Combined action attribute with current location
     *    to get an absolute form target.
     *    @param string $action    Action attribute from form tag.
     *    @param SimpleUrl $base   Page location.
     *    @return SimpleUrl        Absolute form target.
     */
    protected function createAction($action, $page) {
        if (($action === '') || ($action === false)) {
            return $page->expandUrl($page->getUrl());
        }
        return $page->expandUrl(new SimpleUrl($action));;
    }
    
    /**
     *    Absolute URL of the target.
     *    @return SimpleUrl           URL target.
     *    @access public
     */
    function getAction() {
        $url = $this->action;
        if ($this->default_target && ! $url->getTarget()) {
            $url->setTarget($this->default_target);
        }
        return $url;
    }
    
    /**
     *    Creates the encoding for the current values in the
     *    form.
     *    @return SimpleFormEncoding    Request to submit.
     *    @access private
     */
    protected function encode() {
        $class = $this->encoding;
        $encoding = new $class();
        for ($i = 0, $count = count($this->widgets); $i < $count; $i++) {
            $this->widgets[$i]->write($encoding);
        }
        return $encoding;
    }
            
    /**
     *    ID field of form for unique identification.
     *    @return string           Unique tag ID.
     *    @access public
     */
    function getId() {
        return $this->id;
    }
    
    /**
     *    Adds a tag contents to the form.
     *    @param SimpleWidget $tag        Input tag to add.
     *    @access public
     */
    function addWidget($tag) {
        if (strtolower($tag->getAttribute('type')) == 'submit') {
            $this->buttons[] = $tag;
        } elseif (strtolower($tag->getAttribute('type')) == 'image') {
            $this->images[] = $tag;
        } elseif ($tag->getName()) {
            $this->setWidget($tag);
        }
    }
    
    /**
     *    Sets the widget into the form, grouping radio
     *    buttons if any.
     *    @param SimpleWidget $tag   Incoming form control.
     *    @access private
     */
    protected function setWidget($tag) {
        if (strtolower($tag->getAttribute('type')) == 'radio') {
            $this->addRadioButton($tag);
        } elseif (strtolower($tag->getAttribute('type')) == 'checkbox') {
            $this->addCheckbox($tag);
        } else {
            $this->widgets[] = &$tag;
        }
    }
    
    /**
     *    Adds a radio button, building a group if necessary.
     *    @param SimpleRadioButtonTag $tag   Incoming form control.
     *    @access private
     */
    protected function addRadioButton($tag) {
        if (! isset($this->radios[$tag->getName()])) {
            $this->widgets[] = new SimpleRadioGroup();
            $this->radios[$tag->getName()] = count($this->widgets) - 1;
        }
        $this->widgets[$this->radios[$tag->getName()]]->addWidget($tag);
    }
    
    /**
     *    Adds a checkbox, making it a group on a repeated name.
     *    @param SimpleCheckboxTag $tag   Incoming form control.
     *    @access private
     */
    protected function addCheckbox($tag) {
        if (! isset($this->checkboxes[$tag->getName()])) {
            $this->widgets[] = $tag;
            $this->checkboxes[$tag->getName()] = count($this->widgets) - 1;
        } else {
            $index = $this->checkboxes[$tag->getName()];
            if (! SimpleTestCompatibility::isA($this->widgets[$index], 'SimpleCheckboxGroup')) {
                $previous = $this->widgets[$index];
                $this->widgets[$index] = new SimpleCheckboxGroup();
                $this->widgets[$index]->addWidget($previous);
            }
            $this->widgets[$index]->addWidget($tag);
        }
    }
    
    /**
     *    Extracts current value from form.
     *    @param SimpleSelector $selector   Criteria to apply.
     *    @return string/array              Value(s) as string or null
     *                                      if not set.
     *    @access public
     */
    function getValue($selector) {
        for ($i = 0, $count = count($this->widgets); $i < $count; $i++) {
            if ($selector->isMatch($this->widgets[$i])) {
                return $this->widgets[$i]->getValue();
            }
        }
        foreach ($this->buttons as $button) {
            if ($selector->isMatch($button)) {
                return $button->getValue();
            }
        }
        return null;
    }
    
    /**
     *    Sets a widget value within the form.
     *    @param SimpleSelector $selector   Criteria to apply.
     *    @param string $value              Value to input into the widget.
     *    @return boolean                   True if value is legal, false
     *                                      otherwise. If the field is not
     *                                      present, nothing will be set.
     *    @access public
     */
    function setField($selector, $value, $position=false) {
        $success = false;
        $_position = 0;
        for ($i = 0, $count = count($this->widgets); $i < $count; $i++) {
            if ($selector->isMatch($this->widgets[$i])) {
                $_position++;
                if ($position === false or $_position === (int)$position) {
                    if ($this->widgets[$i]->setValue($value)) {
                        $success = true;
                    }
                }
            }
        }
        return $success;
    }
    
    /**
     *    Used by the page object to set widgets labels to
     *    external label tags.
     *    @param SimpleSelector $selector   Criteria to apply.
     *    @access public
     */
    function attachLabelBySelector($selector, $label) {
        for ($i = 0, $count = count($this->widgets); $i < $count; $i++) {
            if ($selector->isMatch($this->widgets[$i])) {
                if (method_exists($this->widgets[$i], 'setLabel')) {
                    $this->widgets[$i]->setLabel($label);
                    return;
                }
            }
        }
    }
    
    /**
     *    Test to see if a form has a submit button.
     *    @param SimpleSelector $selector   Criteria to apply.
     *    @return boolean                   True if present.
     *    @access public
     */
    function hasSubmit($selector) {
        foreach ($this->buttons as $button) {
            if ($selector->isMatch($button)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     *    Test to see if a form has an image control.
     *    @param SimpleSelector $selector   Criteria to apply.
     *    @return boolean                   True if present.
     *    @access public
     */
    function hasImage($selector) {
        foreach ($this->images as $image) {
            if ($selector->isMatch($image)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     *    Gets the submit values for a selected button.
     *    @param SimpleSelector $selector   Criteria to apply.
     *    @param hash $additional           Additional data for the form.
     *    @return SimpleEncoding            Submitted values or false
     *                                      if there is no such button
     *                                      in the form.
     *    @access public
     */
    function submitButton($selector, $additional = false) {
        $additional = $additional ? $additional : array();
        foreach ($this->buttons as $button) {
            if ($selector->isMatch($button)) {
                $encoding = $this->encode();
                $button->write($encoding);
                if ($additional) {
                    $encoding->merge($additional);
                }
                return $encoding;           
            }
        }
        return false;
    }
        
    /**
     *    Gets the submit values for an image.
     *    @param SimpleSelector $selector   Criteria to apply.
     *    @param integer $x                 X-coordinate of click.
     *    @param integer $y                 Y-coordinate of click.
     *    @param hash $additional           Additional data for the form.
     *    @return SimpleEncoding            Submitted values or false
     *                                      if there is no such button in the
     *                                      form.
     *    @access public
     */
    function submitImage($selector, $x, $y, $additional = false) {
        $additional = $additional ? $additional : array();
        foreach ($this->images as $image) {
            if ($selector->isMatch($image)) {
                $encoding = $this->encode();
                $image->write($encoding, $x, $y);
                if ($additional) {
                    $encoding->merge($additional);
                }
                return $encoding;           
            }
        }
        return false;
    }
    
    /**
     *    Simply submits the form without the submit button
     *    value. Used when there is only one button or it
     *    is unimportant.
     *    @return hash           Submitted values.
     *    @access public
     */
    function submit() {
        return $this->encode();
    }
}
?>