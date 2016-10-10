<?php
/**
 *  Base include file for SimpleTest
 *  @package    SimpleTest
 *  @subpackage WebTester
 *  @version    $Id: frames.php 1784 2008-04-26 13:07:14Z pp11 $
 */

/**#@+
 *  include other SimpleTest class files
 */
require_once(dirname(__FILE__) . '/page.php');
require_once(dirname(__FILE__) . '/user_agent.php');
/**#@-*/

/**
 *    A composite page. Wraps a frameset page and
 *    adds subframes. The original page will be
 *    mostly ignored. Implements the SimplePage
 *    interface so as to be interchangeable.
 *    @package SimpleTest
 *    @subpackage WebTester
 */
class SimpleFrameset {
    private $frameset;
    private $frames;
    private $focus;
    private $names;

    /**
     *    Stashes the frameset page. Will make use of the
     *    browser to fetch the sub frames recursively.
     *    @param SimplePage $page        Frameset page.
     */
    function __construct($page) {
        $this->frameset = $page;
        $this->frames = array();
        $this->focus = false;
        $this->names = array();
    }

    /**
     *    Adds a parsed page to the frameset.
     *    @param SimplePage $page    Frame page.
     *    @param string $name        Name of frame in frameset.
     *    @access public
     */
    function addFrame($page, $name = false) {
        $this->frames[] = $page;
        if ($name) {
            $this->names[$name] = count($this->frames) - 1;
        }
    }

    /**
     *    Replaces existing frame with another. If the
     *    frame is nested, then the call is passed down
     *    one level.
     *    @param array $path        Path of frame in frameset.
     *    @param SimplePage $page   Frame source.
     *    @access public
     */
    function setFrame($path, $page) {
        $name = array_shift($path);
        if (isset($this->names[$name])) {
            $index = $this->names[$name];
        } else {
            $index = $name - 1;
        }
        if (count($path) == 0) {
            $this->frames[$index] = &$page;
            return;
        }
        $this->frames[$index]->setFrame($path, $page);
    }

    /**
     *    Accessor for current frame focus. Will be
     *    false if no frame has focus. Will have the nested
     *    frame focus if any.
     *    @return array     Labels or indexes of nested frames.
     *    @access public
     */
    function getFrameFocus() {
        if ($this->focus === false) {
            return array();
        }
        return array_merge(
                array($this->getPublicNameFromIndex($this->focus)),
                $this->frames[$this->focus]->getFrameFocus());
    }

    /**
     *    Turns an internal array index into the frames list
     *    into a public name, or if none, then a one offset
     *    index.
     *    @param integer $subject    Internal index.
     *    @return integer/string     Public name.
     *    @access private
     */
    protected function getPublicNameFromIndex($subject) {
        foreach ($this->names as $name => $index) {
            if ($subject == $index) {
                return $name;
            }
        }
        return $subject + 1;
    }

    /**
     *    Sets the focus by index. The integer index starts from 1.
     *    If already focused and the target frame also has frames,
     *    then the nested frame will be focused.
     *    @param integer $choice    Chosen frame.
     *    @return boolean           True if frame exists.
     *    @access public
     */
    function setFrameFocusByIndex($choice) {
        if (is_integer($this->focus)) {
            if ($this->frames[$this->focus]->hasFrames()) {
                return $this->frames[$this->focus]->setFrameFocusByIndex($choice);
            }
        }
        if (($choice < 1) || ($choice > count($this->frames))) {
            return false;
        }
        $this->focus = $choice - 1;
        return true;
    }

    /**
     *    Sets the focus by name. If already focused and the
     *    target frame also has frames, then the nested frame
     *    will be focused.
     *    @param string $name    Chosen frame.
     *    @return boolean        True if frame exists.
     *    @access public
     */
    function setFrameFocus($name) {
        if (is_integer($this->focus)) {
            if ($this->frames[$this->focus]->hasFrames()) {
                return $this->frames[$this->focus]->setFrameFocus($name);
            }
        }
        if (in_array($name, array_keys($this->names))) {
            $this->focus = $this->names[$name];
            return true;
        }
        return false;
    }

    /**
     *    Clears the frame focus.
     *    @access public
     */
    function clearFrameFocus() {
        $this->focus = false;
        $this->clearNestedFramesFocus();
    }

    /**
     *    Clears the frame focus for any nested frames.
     *    @access private
     */
    protected function clearNestedFramesFocus() {
        for ($i = 0; $i < count($this->frames); $i++) {
            $this->frames[$i]->clearFrameFocus();
        }
    }

    /**
     *    Test for the presence of a frameset.
     *    @return boolean        Always true.
     *    @access public
     */
    function hasFrames() {
        return true;
    }

    /**
     *    Accessor for frames information.
     *    @return array/string      Recursive hash of frame URL strings.
     *                              The key is either a numerical
     *                              index or the name attribute.
     *    @access public
     */
    function getFrames() {
        $report = array();
        for ($i = 0; $i < count($this->frames); $i++) {
            $report[$this->getPublicNameFromIndex($i)] =
                    $this->frames[$i]->getFrames();
        }
        return $report;
    }

    /**
     *    Accessor for raw text of either all the pages or
     *    the frame in focus.
     *    @return string        Raw unparsed content.
     *    @access public
     */
    function getRaw() {
        if (is_integer($this->focus)) {
            return $this->frames[$this->focus]->getRaw();
        }
        $raw = '';
        for ($i = 0; $i < count($this->frames); $i++) {
            $raw .= $this->frames[$i]->getRaw();
        }
        return $raw;
    }

    /**
     *    Accessor for plain text of either all the pages or
     *    the frame in focus.
     *    @return string        Plain text content.
     *    @access public
     */
    function getText() {
        if (is_integer($this->focus)) {
            return $this->frames[$this->focus]->getText();
        }
        $raw = '';
        for ($i = 0; $i < count($this->frames); $i++) {
            $raw .= ' ' . $this->frames[$i]->getText();
        }
        return trim($raw);
    }

    /**
     *    Accessor for last error.
     *    @return string        Error from last response.
     *    @access public
     */
    function getTransportError() {
        if (is_integer($this->focus)) {
            return $this->frames[$this->focus]->getTransportError();
        }
        return $this->frameset->getTransportError();
    }

    /**
     *    Request method used to fetch this frame.
     *    @return string      GET, POST or HEAD.
     *    @access public
     */
    function getMethod() {
        if (is_integer($this->focus)) {
            return $this->frames[$this->focus]->getMethod();
        }
        return $this->frameset->getMethod();
    }

    /**
     *    Original resource name.
     *    @return SimpleUrl        Current url.
     *    @access public
     */
    function getUrl() {
        if (is_integer($this->focus)) {
            $url = $this->frames[$this->focus]->getUrl();
            $url->setTarget($this->getPublicNameFromIndex($this->focus));
        } else {
            $url = $this->frameset->getUrl();
        }
        return $url;
    }

    /**
     *    Page base URL.
     *    @return SimpleUrl        Current url.
     *    @access public
     */
    function getBaseUrl() {
        if (is_integer($this->focus)) {
            $url = $this->frames[$this->focus]->getBaseUrl();
        } else {
            $url = $this->frameset->getBaseUrl();
        }
        return $url;
    }

    /**
     *    Expands expandomatic URLs into fully qualified
     *    URLs for the frameset page.
     *    @param SimpleUrl $url        Relative URL.
     *    @return SimpleUrl            Absolute URL.
     *    @access public
     */
    function expandUrl($url) {
        return $this->frameset->expandUrl($url);
    }

    /**
     *    Original request data.
     *    @return mixed              Sent content.
     *    @access public
     */
    function getRequestData() {
        if (is_integer($this->focus)) {
            return $this->frames[$this->focus]->getRequestData();
        }
        return $this->frameset->getRequestData();
    }

    /**
     *    Accessor for current MIME type.
     *    @return string    MIME type as string; e.g. 'text/html'
     *    @access public
     */
    function getMimeType() {
        if (is_integer($this->focus)) {
            return $this->frames[$this->focus]->getMimeType();
        }
        return $this->frameset->getMimeType();
    }

    /**
     *    Accessor for last response code.
     *    @return integer    Last HTTP response code received.
     *    @access public
     */
    function getResponseCode() {
        if (is_integer($this->focus)) {
            return $this->frames[$this->focus]->getResponseCode();
        }
        return $this->frameset->getResponseCode();
    }

    /**
     *    Accessor for last Authentication type. Only valid
     *    straight after a challenge (401).
     *    @return string    Description of challenge type.
     *    @access public
     */
    function getAuthentication() {
        if (is_integer($this->focus)) {
            return $this->frames[$this->focus]->getAuthentication();
        }
        return $this->frameset->getAuthentication();
    }

    /**
     *    Accessor for last Authentication realm. Only valid
     *    straight after a challenge (401).
     *    @return string    Name of security realm.
     *    @access public
     */
    function getRealm() {
        if (is_integer($this->focus)) {
            return $this->frames[$this->focus]->getRealm();
        }
        return $this->frameset->getRealm();
    }

    /**
     *    Accessor for outgoing header information.
     *    @return string      Header block.
     *    @access public
     */
    function getRequest() {
        if (is_integer($this->focus)) {
            return $this->frames[$this->focus]->getRequest();
        }
        return $this->frameset->getRequest();
    }

    /**
     *    Accessor for raw header information.
     *    @return string      Header block.
     *    @access public
     */
    function getHeaders() {
        if (is_integer($this->focus)) {
            return $this->frames[$this->focus]->getHeaders();
        }
        return $this->frameset->getHeaders();
    }

    /**
     *    Accessor for parsed title.
     *    @return string     Title or false if no title is present.
     *    @access public
     */
    function getTitle() {
        return $this->frameset->getTitle();
    }

    /**
     *    Accessor for a list of all fixed links.
     *    @return array   List of urls as strings.
     *    @access public
     */
    function getUrls() {
        if (is_integer($this->focus)) {
            return $this->frames[$this->focus]->getUrls();
        }
        $urls = array();
        foreach ($this->frames as $frame) {
            $urls = array_merge($urls, $frame->getUrls());
        }
        return array_values(array_unique($urls));
    }

    /**
     *    Accessor for URLs by the link label. Label will match
     *    regardess of whitespace issues and case.
     *    @param string $label    Text of link.
     *    @return array           List of links with that label.
     *    @access public
     */
    function getUrlsByLabel($label) {
        if (is_integer($this->focus)) {
            return $this->tagUrlsWithFrame(
                    $this->frames[$this->focus]->getUrlsByLabel($label),
                    $this->focus);
        }
        $urls = array();
        foreach ($this->frames as $index => $frame) {
            $urls = array_merge(
                    $urls,
                    $this->tagUrlsWithFrame(
                                $frame->getUrlsByLabel($label),
                                $index));
        }
        return $urls;
    }

    /**
     *    Accessor for a URL by the id attribute. If in a frameset
     *    then the first link found with that ID attribute is
     *    returned only. Focus on a frame if you want one from
     *    a specific part of the frameset.
     *    @param string $id       Id attribute of link.
     *    @return string          URL with that id.
     *    @access public
     */
    function getUrlById($id) {
        foreach ($this->frames as $index => $frame) {
            if ($url = $frame->getUrlById($id)) {
                if (! $url->gettarget()) {
                    $url->setTarget($this->getPublicNameFromIndex($index));
                }
                return $url;
            }
        }
        return false;
    }

    /**
     *    Attaches the intended frame index to a list of URLs.
     *    @param array $urls        List of SimpleUrls.
     *    @param string $frame      Name of frame or index.
     *    @return array             List of tagged URLs.
     *    @access private
     */
    protected function tagUrlsWithFrame($urls, $frame) {
        $tagged = array();
        foreach ($urls as $url) {
            if (! $url->getTarget()) {
                $url->setTarget($this->getPublicNameFromIndex($frame));
            }
            $tagged[] = $url;
        }
        return $tagged;
    }

    /**
     *    Finds a held form by button label. Will only
     *    search correctly built forms.
     *    @param SimpleSelector $selector       Button finder.
     *    @return SimpleForm                    Form object containing
     *                                          the button.
     *    @access public
     */
    function getFormBySubmit($selector) {
        return $this->findForm('getFormBySubmit', $selector);
    }

    /**
     *    Finds a held form by image using a selector.
     *    Will only search correctly built forms. The first
     *    form found either within the focused frame, or
     *    across frames, will be the one returned.
     *    @param SimpleSelector $selector  Image finder.
     *    @return SimpleForm               Form object containing
     *                                     the image.
     *    @access public
     */
    function getFormByImage($selector) {
        return $this->findForm('getFormByImage', $selector);
    }

    /**
     *    Finds a held form by the form ID. A way of
     *    identifying a specific form when we have control
     *    of the HTML code. The first form found
     *    either within the focused frame, or across frames,
     *    will be the one returned.
     *    @param string $id     Form label.
     *    @return SimpleForm    Form object containing the matching ID.
     *    @access public
     */
    function getFormById($id) {
        return $this->findForm('getFormById', $id);
    }

    /**
        *    General form finder. Will search all the frames or
        *    just the one in focus.
        *    @param string $method    Method to use to find in a page.
        *    @param string $attribute Label, name or ID.
        *    @return SimpleForm    Form object containing the matching ID.
        *    @access private
        */
    protected function findForm($method, $attribute) {
        if (is_integer($this->focus)) {
            return $this->findFormInFrame(
                    $this->frames[$this->focus],
                    $this->focus,
                    $method,
                    $attribute);
        }
        for ($i = 0; $i < count($this->frames); $i++) {
            $form = $this->findFormInFrame(
                    $this->frames[$i],
                    $i,
                    $method,
                    $attribute);
            if ($form) {
                return $form;
            }
        }
        $null = null;
        return $null;
    }

    /**
     *    Finds a form in a page using a form finding method. Will
     *    also tag the form with the frame name it belongs in.
     *    @param SimplePage $page  Page content of frame.
     *    @param integer $index    Internal frame representation.
     *    @param string $method    Method to use to find in a page.
     *    @param string $attribute Label, name or ID.
     *    @return SimpleForm       Form object containing the matching ID.
     *    @access private
     */
    protected function findFormInFrame($page, $index, $method, $attribute) {
        $form = $this->frames[$index]->$method($attribute);
        if (isset($form)) {
            $form->setDefaultTarget($this->getPublicNameFromIndex($index));
        }
        return $form;
    }

    /**
     *    Sets a field on each form in which the field is
     *    available.
     *    @param SimpleSelector $selector    Field finder.
     *    @param string $value               Value to set field to.
     *    @return boolean                    True if value is valid.
     *    @access public
     */
    function setField($selector, $value) {
        if (is_integer($this->focus)) {
            $this->frames[$this->focus]->setField($selector, $value);
        } else {
            for ($i = 0; $i < count($this->frames); $i++) {
                $this->frames[$i]->setField($selector, $value);
            }
        }
    }

    /**
     *    Accessor for a form element value within a page.
     *    @param SimpleSelector $selector    Field finder.
     *    @return string/boolean             A string if the field is
     *                                       present, false if unchecked
     *                                       and null if missing.
     *    @access public
     */
    function getField($selector) {
        for ($i = 0; $i < count($this->frames); $i++) {
            $value = $this->frames[$i]->getField($selector);
            if (isset($value)) {
                return $value;
            }
        }
        return null;
    }
}
?>