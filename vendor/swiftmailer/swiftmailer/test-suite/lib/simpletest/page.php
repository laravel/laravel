<?php
/**
 *  Base include file for SimpleTest
 *  @package    SimpleTest
 *  @subpackage WebTester
 *  @version    $Id: page.php 1786 2008-04-26 17:32:20Z pp11 $
 */

/**#@+
    *   include other SimpleTest class files
    */
require_once(dirname(__FILE__) . '/http.php');
require_once(dirname(__FILE__) . '/parser.php');
require_once(dirname(__FILE__) . '/tag.php');
require_once(dirname(__FILE__) . '/form.php');
require_once(dirname(__FILE__) . '/selector.php');
/**#@-*/

/**
 *    Creates tags and widgets given HTML tag
 *    attributes.
 *    @package SimpleTest
 *    @subpackage WebTester
 */
class SimpleTagBuilder {

    /**
     *    Factory for the tag objects. Creates the
     *    appropriate tag object for the incoming tag name
     *    and attributes.
     *    @param string $name        HTML tag name.
     *    @param hash $attributes    Element attributes.
     *    @return SimpleTag          Tag object.
     *    @access public
     */
    function createTag($name, $attributes) {
        static $map = array(
                'a' => 'SimpleAnchorTag',
                'title' => 'SimpleTitleTag',
                'base' => 'SimpleBaseTag',
                'button' => 'SimpleButtonTag',
                'textarea' => 'SimpleTextAreaTag',
                'option' => 'SimpleOptionTag',
                'label' => 'SimpleLabelTag',
                'form' => 'SimpleFormTag',
                'frame' => 'SimpleFrameTag');
        $attributes = $this->keysToLowerCase($attributes);
        if (array_key_exists($name, $map)) {
            $tag_class = $map[$name];
            return new $tag_class($attributes);
        } elseif ($name == 'select') {
            return $this->createSelectionTag($attributes);
        } elseif ($name == 'input') {
            return $this->createInputTag($attributes);
        }
        return new SimpleTag($name, $attributes);
    }

    /**
     *    Factory for selection fields.
     *    @param hash $attributes    Element attributes.
     *    @return SimpleTag          Tag object.
     *    @access protected
     */
    protected function createSelectionTag($attributes) {
        if (isset($attributes['multiple'])) {
            return new MultipleSelectionTag($attributes);
        }
        return new SimpleSelectionTag($attributes);
    }

    /**
     *    Factory for input tags.
     *    @param hash $attributes    Element attributes.
     *    @return SimpleTag          Tag object.
     *    @access protected
     */
    protected function createInputTag($attributes) {
        if (! isset($attributes['type'])) {
            return new SimpleTextTag($attributes);
        }
        $type = strtolower(trim($attributes['type']));
        $map = array(
                'submit' => 'SimpleSubmitTag',
                'image' => 'SimpleImageSubmitTag',
                'checkbox' => 'SimpleCheckboxTag',
                'radio' => 'SimpleRadioButtonTag',
                'text' => 'SimpleTextTag',
                'hidden' => 'SimpleTextTag',
                'password' => 'SimpleTextTag',
                'file' => 'SimpleUploadTag');
        if (array_key_exists($type, $map)) {
            $tag_class = $map[$type];
            return new $tag_class($attributes);
        }
        return false;
    }

    /**
     *    Make the keys lower case for case insensitive look-ups.
     *    @param hash $map   Hash to convert.
     *    @return hash       Unchanged values, but keys lower case.
     *    @access private
     */
    protected function keysToLowerCase($map) {
        $lower = array();
        foreach ($map as $key => $value) {
            $lower[strtolower($key)] = $value;
        }
        return $lower;
    }
}

/**
 *    SAX event handler. Maintains a list of
 *    open tags and dispatches them as they close.
 *    @package SimpleTest
 *    @subpackage WebTester
 */
class SimplePageBuilder extends SimpleSaxListener {
    private $tags;
    private $page;
    private $private_content_tag;

    /**
     *    Sets the builder up empty.
     *    @access public
     */
    function __construct() {
        parent::__construct();
    }
    
    /**
     *    Frees up any references so as to allow the PHP garbage
     *    collection from unset() to work.
     *    @access public
     */
    function free() {
        unset($this->tags);
        unset($this->page);
        unset($this->private_content_tags);
    }

    /**
     *    Reads the raw content and send events
     *    into the page to be built.
     *    @param $response SimpleHttpResponse  Fetched response.
     *    @return SimplePage                   Newly parsed page.
     *    @access public
     */
    function parse($response) {
        $this->tags = array();
        $this->page = $this->createPage($response);
        $parser = $this->createParser($this);
        $parser->parse($response->getContent());
        $this->page->acceptPageEnd();
        return $this->page;
    }

    /**
     *    Creates an empty page.
     *    @return SimplePage        New unparsed page.
     *    @access protected
     */
    protected function createPage($response) {
        return new SimplePage($response);
    }

    /**
     *    Creates the parser used with the builder.
     *    @param $listener SimpleSaxListener   Target of parser.
     *    @return SimpleSaxParser              Parser to generate
     *                                         events for the builder.
     *    @access protected
     */
    protected function createParser(&$listener) {
        return new SimpleHtmlSaxParser($listener);
    }
    
    /**
     *    Start of element event. Opens a new tag.
     *    @param string $name         Element name.
     *    @param hash $attributes     Attributes without content
     *                                are marked as true.
     *    @return boolean             False on parse error.
     *    @access public
     */
    function startElement($name, $attributes) {
        $factory = new SimpleTagBuilder();
        $tag = $factory->createTag($name, $attributes);
        if (! $tag) {
            return true;
        }
        if ($tag->getTagName() == 'label') {
            $this->page->acceptLabelStart($tag);
            $this->openTag($tag);
            return true;
        }
        if ($tag->getTagName() == 'form') {
            $this->page->acceptFormStart($tag);
            return true;
        }
        if ($tag->getTagName() == 'frameset') {
            $this->page->acceptFramesetStart($tag);
            return true;
        }
        if ($tag->getTagName() == 'frame') {
            $this->page->acceptFrame($tag);
            return true;
        }
        if ($tag->isPrivateContent() && ! isset($this->private_content_tag)) {
            $this->private_content_tag = &$tag;
        }
        if ($tag->expectEndTag()) {
            $this->openTag($tag);
            return true;
        }
        $this->page->acceptTag($tag);
        return true;
    }

    /**
     *    End of element event.
     *    @param string $name        Element name.
     *    @return boolean            False on parse error.
     *    @access public
     */
    function endElement($name) {
        if ($name == 'label') {
            $this->page->acceptLabelEnd();
            return true;
        }
        if ($name == 'form') {
            $this->page->acceptFormEnd();
            return true;
        }
        if ($name == 'frameset') {
            $this->page->acceptFramesetEnd();
            return true;
        }
        if ($this->hasNamedTagOnOpenTagStack($name)) {
            $tag = array_pop($this->tags[$name]);
            if ($tag->isPrivateContent() && $this->private_content_tag->getTagName() == $name) {
                unset($this->private_content_tag);
            }
            $this->addContentTagToOpenTags($tag);
            $this->page->acceptTag($tag);
            return true;
        }
        return true;
    }

    /**
     *    Test to see if there are any open tags awaiting
     *    closure that match the tag name.
     *    @param string $name        Element name.
     *    @return boolean            True if any are still open.
     *    @access private
     */
    protected function hasNamedTagOnOpenTagStack($name) {
        return isset($this->tags[$name]) && (count($this->tags[$name]) > 0);
    }

    /**
     *    Unparsed, but relevant data. The data is added
     *    to every open tag.
     *    @param string $text        May include unparsed tags.
     *    @return boolean            False on parse error.
     *    @access public
     */
    function addContent($text) {
        if (isset($this->private_content_tag)) {
            $this->private_content_tag->addContent($text);
        } else {
            $this->addContentToAllOpenTags($text);
        }
        return true;
    }

    /**
     *    Any content fills all currently open tags unless it
     *    is part of an option tag.
     *    @param string $text        May include unparsed tags.
     *    @access private
     */
    protected function addContentToAllOpenTags($text) {
        foreach (array_keys($this->tags) as $name) {
            for ($i = 0, $count = count($this->tags[$name]); $i < $count; $i++) {
                $this->tags[$name][$i]->addContent($text);
            }
        }
    }

    /**
     *    Parsed data in tag form. The parsed tag is added
     *    to every open tag. Used for adding options to select
     *    fields only.
     *    @param SimpleTag $tag        Option tags only.
     *    @access private
     */
    protected function addContentTagToOpenTags(&$tag) {
        if ($tag->getTagName() != 'option') {
            return;
        }
        foreach (array_keys($this->tags) as $name) {
            for ($i = 0, $count = count($this->tags[$name]); $i < $count; $i++) {
                $this->tags[$name][$i]->addTag($tag);
            }
        }
    }

    /**
     *    Opens a tag for receiving content. Multiple tags
     *    will be receiving input at the same time.
     *    @param SimpleTag $tag        New content tag.
     *    @access private
     */
    protected function openTag($tag) {
        $name = $tag->getTagName();
        if (! in_array($name, array_keys($this->tags))) {
            $this->tags[$name] = array();
        }
        $this->tags[$name][] = $tag;
    }
}

/**
 *    A wrapper for a web page.
 *    @package SimpleTest
 *    @subpackage WebTester
 */
class SimplePage {
    private $links;
    private $title;
    private $last_widget;
    private $label;
    private $left_over_labels;
    private $open_forms;
    private $complete_forms;
    private $frameset;
    private $frames;
    private $frameset_nesting_level;
    private $transport_error;
    private $raw;
    private $text;
    private $sent;
    private $headers;
    private $method;
    private $url;
    private $base = false;
    private $request_data;

    /**
     *    Parses a page ready to access it's contents.
     *    @param SimpleHttpResponse $response     Result of HTTP fetch.
     *    @access public
     */
    function __construct($response = false) {
        $this->links = array();
        $this->title = false;
        $this->left_over_labels = array();
        $this->open_forms = array();
        $this->complete_forms = array();
        $this->frameset = false;
        $this->frames = array();
        $this->frameset_nesting_level = 0;
        $this->text = false;
        if ($response) {
            $this->extractResponse($response);
        } else {
            $this->noResponse();
        }
    }

    /**
     *    Extracts all of the response information.
     *    @param SimpleHttpResponse $response    Response being parsed.
     *    @access private
     */
    protected function extractResponse($response) {
        $this->transport_error = $response->getError();
        $this->raw = $response->getContent();
        $this->sent = $response->getSent();
        $this->headers = $response->getHeaders();
        $this->method = $response->getMethod();
        $this->url = $response->getUrl();
        $this->request_data = $response->getRequestData();
    }

    /**
     *    Sets up a missing response.
     *    @access private
     */
    protected function noResponse() {
        $this->transport_error = 'No page fetched yet';
        $this->raw = false;
        $this->sent = false;
        $this->headers = false;
        $this->method = 'GET';
        $this->url = false;
        $this->request_data = false;
    }

    /**
     *    Original request as bytes sent down the wire.
     *    @return mixed              Sent content.
     *    @access public
     */
    function getRequest() {
        return $this->sent;
    }

    /**
     *    Accessor for raw text of page.
     *    @return string        Raw unparsed content.
     *    @access public
     */
    function getRaw() {
        return $this->raw;
    }

    /**
     *    Accessor for plain text of page as a text browser
     *    would see it.
     *    @return string        Plain text of page.
     *    @access public
     */
    function getText() {
        if (! $this->text) {
            $this->text = SimpleHtmlSaxParser::normalise($this->raw);
        }
        return $this->text;
    }

    /**
     *    Accessor for raw headers of page.
     *    @return string       Header block as text.
     *    @access public
     */
    function getHeaders() {
        if ($this->headers) {
            return $this->headers->getRaw();
        }
        return false;
    }

    /**
     *    Original request method.
     *    @return string        GET, POST or HEAD.
     *    @access public
     */
    function getMethod() {
        return $this->method;
    }

    /**
     *    Original resource name.
     *    @return SimpleUrl        Current url.
     *    @access public
     */
    function getUrl() {
        return $this->url;
    }

    /**
     *    Base URL if set via BASE tag page url otherwise
     *    @return SimpleUrl        Base url.
     *    @access public
     */
    function getBaseUrl() {
        return $this->base;
    }

    /**
     *    Original request data.
     *    @return mixed              Sent content.
     *    @access public
     */
    function getRequestData() {
        return $this->request_data;
    }

    /**
     *    Accessor for last error.
     *    @return string        Error from last response.
     *    @access public
     */
    function getTransportError() {
        return $this->transport_error;
    }

    /**
     *    Accessor for current MIME type.
     *    @return string    MIME type as string; e.g. 'text/html'
     *    @access public
     */
    function getMimeType() {
        if ($this->headers) {
            return $this->headers->getMimeType();
        }
        return false;
    }

    /**
     *    Accessor for HTTP response code.
     *    @return integer    HTTP response code received.
     *    @access public
     */
    function getResponseCode() {
        if ($this->headers) {
            return $this->headers->getResponseCode();
        }
        return false;
    }

    /**
     *    Accessor for last Authentication type. Only valid
     *    straight after a challenge (401).
     *    @return string    Description of challenge type.
     *    @access public
     */
    function getAuthentication() {
        if ($this->headers) {
            return $this->headers->getAuthentication();
        }
        return false;
    }

    /**
     *    Accessor for last Authentication realm. Only valid
     *    straight after a challenge (401).
     *    @return string    Name of security realm.
     *    @access public
     */
    function getRealm() {
        if ($this->headers) {
            return $this->headers->getRealm();
        }
        return false;
    }

    /**
     *    Accessor for current frame focus. Will be
     *    false as no frames.
     *    @return array    Always empty.
     *    @access public
     */
    function getFrameFocus() {
        return array();
    }

    /**
     *    Sets the focus by index. The integer index starts from 1.
     *    @param integer $choice    Chosen frame.
     *    @return boolean           Always false.
     *    @access public
     */
    function setFrameFocusByIndex($choice) {
        return false;
    }

    /**
     *    Sets the focus by name. Always fails for a leaf page.
     *    @param string $name    Chosen frame.
     *    @return boolean        False as no frames.
     *    @access public
     */
    function setFrameFocus($name) {
        return false;
    }

    /**
     *    Clears the frame focus. Does nothing for a leaf page.
     *    @access public
     */
    function clearFrameFocus() {
    }

    /**
     *    Adds a tag to the page.
     *    @param SimpleTag $tag        Tag to accept.
     *    @access public
     */
    function acceptTag($tag) {
        if ($tag->getTagName() == "a") {
            $this->addLink($tag);
        } elseif ($tag->getTagName() == "base") {
            $this->setBase($tag);
        } elseif ($tag->getTagName() == "title") {
            $this->setTitle($tag);
        } elseif ($this->isFormElement($tag->getTagName())) {
            for ($i = 0; $i < count($this->open_forms); $i++) {
                $this->open_forms[$i]->addWidget($tag);
            }
            $this->last_widget = &$tag;
        }
    }

    /**
     *    Opens a label for a described widget.
     *    @param SimpleFormTag $tag      Tag to accept.
     *    @access public
     */
    function acceptLabelStart($tag) {
        $this->label = $tag;
        unset($this->last_widget);
    }

    /**
     *    Closes the most recently opened label.
     *    @access public
     */
    function acceptLabelEnd() {
        if (isset($this->label)) {
            if (isset($this->last_widget)) {
                $this->last_widget->setLabel($this->label->getText());
                unset($this->last_widget);
            } else {
                $this->left_over_labels[] = SimpleTestCompatibility::copy($this->label);
            }
            unset($this->label);
        }
    }

    /**
     *    Tests to see if a tag is a possible form
     *    element.
     *    @param string $name     HTML element name.
     *    @return boolean         True if form element.
     *    @access private
     */
    protected function isFormElement($name) {
        return in_array($name, array('input', 'button', 'textarea', 'select'));
    }

    /**
     *    Opens a form. New widgets go here.
     *    @param SimpleFormTag $tag      Tag to accept.
     *    @access public
     */
    function acceptFormStart($tag) {
        $this->open_forms[] = new SimpleForm($tag, $this);
    }

    /**
     *    Closes the most recently opened form.
     *    @access public
     */
    function acceptFormEnd() {
        if (count($this->open_forms)) {
            $this->complete_forms[] = array_pop($this->open_forms);
        }
    }

    /**
     *    Opens a frameset. A frameset may contain nested
     *    frameset tags.
     *    @param SimpleFramesetTag $tag      Tag to accept.
     *    @access public
     */
    function acceptFramesetStart($tag) {
        if (! $this->isLoadingFrames()) {
            $this->frameset = $tag;
        }
        $this->frameset_nesting_level++;
    }

    /**
     *    Closes the most recently opened frameset.
     *    @access public
     */
    function acceptFramesetEnd() {
        if ($this->isLoadingFrames()) {
            $this->frameset_nesting_level--;
        }
    }

    /**
     *    Takes a single frame tag and stashes it in
     *    the current frame set.
     *    @param SimpleFrameTag $tag      Tag to accept.
     *    @access public
     */
    function acceptFrame($tag) {
        if ($this->isLoadingFrames()) {
            if ($tag->getAttribute('src')) {
                $this->frames[] = $tag;
            }
        }
    }

    /**
     *    Test to see if in the middle of reading
     *    a frameset.
     *    @return boolean        True if inframeset.
     *    @access private
     */
    protected function isLoadingFrames() {
        if (! $this->frameset) {
            return false;
        }
        return ($this->frameset_nesting_level > 0);
    }

    /**
     *    Test to see if link is an absolute one.
     *    @param string $url     Url to test.
     *    @return boolean        True if absolute.
     *    @access protected
     */
    protected function linkIsAbsolute($url) {
        $parsed = new SimpleUrl($url);
        return (boolean)($parsed->getScheme() && $parsed->getHost());
    }

    /**
     *    Adds a link to the page.
     *    @param SimpleAnchorTag $tag      Link to accept.
     *    @access protected
     */
    protected function addLink($tag) {
        $this->links[] = $tag;
    }

    /**
     *    Marker for end of complete page. Any work in
     *    progress can now be closed.
     *    @access public
     */
    function acceptPageEnd() {
        while (count($this->open_forms)) {
            $this->complete_forms[] = array_pop($this->open_forms);
        }
        foreach ($this->left_over_labels as $label) {
            for ($i = 0, $count = count($this->complete_forms); $i < $count; $i++) {
                $this->complete_forms[$i]->attachLabelBySelector(
                        new SimpleById($label->getFor()),
                        $label->getText());
            }
        }
    }

    /**
     *    Test for the presence of a frameset.
     *    @return boolean        True if frameset.
     *    @access public
     */
    function hasFrames() {
        return (boolean)$this->frameset;
    }

    /**
     *    Accessor for frame name and source URL for every frame that
     *    will need to be loaded. Immediate children only.
     *    @return boolean/array     False if no frameset or
     *                              otherwise a hash of frame URLs.
     *                              The key is either a numerical
     *                              base one index or the name attribute.
     *    @access public
     */
    function getFrameset() {
        if (! $this->frameset) {
            return false;
        }
        $urls = array();
        for ($i = 0; $i < count($this->frames); $i++) {
            $name = $this->frames[$i]->getAttribute('name');
            $url = new SimpleUrl($this->frames[$i]->getAttribute('src'));
            $urls[$name ? $name : $i + 1] = $this->expandUrl($url);
        }
        return $urls;
    }

    /**
     *    Fetches a list of loaded frames.
     *    @return array/string    Just the URL for a single page.
     *    @access public
     */
    function getFrames() {
        $url = $this->expandUrl($this->getUrl());
        return $url->asString();
    }

    /**
     *    Accessor for a list of all links.
     *    @return array   List of urls with scheme of
     *                    http or https and hostname.
     *    @access public
     */
    function getUrls() {
        $all = array();
        foreach ($this->links as $link) {
            $url = $this->getUrlFromLink($link);
            $all[] = $url->asString();
        }
        return $all;
    }

    /**
     *    Accessor for URLs by the link label. Label will match
     *    regardess of whitespace issues and case.
     *    @param string $label    Text of link.
     *    @return array           List of links with that label.
     *    @access public
     */
    function getUrlsByLabel($label) {
        $matches = array();
        foreach ($this->links as $link) {
            if ($link->getText() == $label) {
                $matches[] = $this->getUrlFromLink($link);
            }
        }
        return $matches;
    }

    /**
     *    Accessor for a URL by the id attribute.
     *    @param string $id       Id attribute of link.
     *    @return SimpleUrl       URL with that id of false if none.
     *    @access public
     */
    function getUrlById($id) {
        foreach ($this->links as $link) {
            if ($link->getAttribute('id') === (string)$id) {
                return $this->getUrlFromLink($link);
            }
        }
        return false;
    }

    /**
     *    Converts a link tag into a target URL.
     *    @param SimpleAnchor $link    Parsed link.
     *    @return SimpleUrl            URL with frame target if any.
     *    @access private
     */
    protected function getUrlFromLink($link) {
        $url = $this->expandUrl($link->getHref());
        if ($link->getAttribute('target')) {
            $url->setTarget($link->getAttribute('target'));
        }
        return $url;
    }

    /**
     *    Expands expandomatic URLs into fully qualified
     *    URLs.
     *    @param SimpleUrl $url        Relative URL.
     *    @return SimpleUrl            Absolute URL.
     *    @access public
     */
    function expandUrl($url) {
        if (! is_object($url)) {
            $url = new SimpleUrl($url);
        }
        $location = $this->getBaseUrl() ? $this->getBaseUrl() : new SimpleUrl();
        return $url->makeAbsolute($location->makeAbsolute($this->getUrl()));
    }

    /**
     *    Sets the base url for the page.
     *    @param SimpleTag $tag    Base URL for page.
     *    @access protected
     */
    protected function setBase($tag) {
        $url = $tag->getAttribute('href');
        $this->base = new SimpleUrl($url);
    }

    /**
     *    Sets the title tag contents.
     *    @param SimpleTitleTag $tag    Title of page.
     *    @access protected
     */
    protected function setTitle($tag) {
        $this->title = $tag;
    }

    /**
     *    Accessor for parsed title.
     *    @return string     Title or false if no title is present.
     *    @access public
     */
    function getTitle() {
        if ($this->title) {
            return $this->title->getText();
        }
        return false;
    }

    /**
     *    Finds a held form by button label. Will only
     *    search correctly built forms.
     *    @param SimpleSelector $selector       Button finder.
     *    @return SimpleForm                    Form object containing
     *                                          the button.
     *    @access public
     */
    function &getFormBySubmit($selector) {
        for ($i = 0; $i < count($this->complete_forms); $i++) {
            if ($this->complete_forms[$i]->hasSubmit($selector)) {
                return $this->complete_forms[$i];
            }
        }
        $null = null;
        return $null;
    }

    /**
     *    Finds a held form by image using a selector.
     *    Will only search correctly built forms.
     *    @param SimpleSelector $selector  Image finder.
     *    @return SimpleForm               Form object containing
     *                                     the image.
     *    @access public
     */
    function getFormByImage($selector) {
        for ($i = 0; $i < count($this->complete_forms); $i++) {
            if ($this->complete_forms[$i]->hasImage($selector)) {
                return $this->complete_forms[$i];
            }
        }
        return null;
    }

    /**
     *    Finds a held form by the form ID. A way of
     *    identifying a specific form when we have control
     *    of the HTML code.
     *    @param string $id     Form label.
     *    @return SimpleForm    Form object containing the matching ID.
     *    @access public
     */
    function getFormById($id) {
        for ($i = 0; $i < count($this->complete_forms); $i++) {
            if ($this->complete_forms[$i]->getId() == $id) {
                return $this->complete_forms[$i];
            }
        }
        return null;
    }

    /**
     *    Sets a field on each form in which the field is
     *    available.
     *    @param SimpleSelector $selector    Field finder.
     *    @param string $value               Value to set field to.
     *    @return boolean                    True if value is valid.
     *    @access public
     */
    function setField($selector, $value, $position=false) {
        $is_set = false;
        for ($i = 0; $i < count($this->complete_forms); $i++) {
            if ($this->complete_forms[$i]->setField($selector, $value, $position)) {
                $is_set = true;
            }
        }
        return $is_set;
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
        for ($i = 0; $i < count($this->complete_forms); $i++) {
            $value = $this->complete_forms[$i]->getValue($selector);
            if (isset($value)) {
                return $value;
            }
        }
        return null;
    }
}
?>