<?php
/**
 *  base include file for SimpleTest
 *  @package    SimpleTest
 *  @subpackage WebTester
 *  @version    $Id: url.php 1789 2008-04-27 11:24:52Z pp11 $
 */

/**#@+
 *  include other SimpleTest class files
 */
require_once(dirname(__FILE__) . '/encoding.php');
/**#@-*/

/**
 *    URL parser to replace parse_url() PHP function which
 *    got broken in PHP 4.3.0. Adds some browser specific
 *    functionality such as expandomatics.
 *    Guesses a bit trying to separate the host from
 *    the path and tries to keep a raw, possibly unparsable,
 *    request string as long as possible.
 *    @package SimpleTest
 *    @subpackage WebTester
 */
class SimpleUrl {
    private $scheme;
    private $username;
    private $password;
    private $host;
    private $port;
    public $path;
    private $request;
    private $fragment;
    private $x;
    private $y;
    private $target;
    private $raw = false;
    
    /**
     *    Constructor. Parses URL into sections.
     *    @param string $url        Incoming URL.
     *    @access public
     */
    function __construct($url = '') {
        list($x, $y) = $this->chompCoordinates($url);
        $this->setCoordinates($x, $y);
        $this->scheme = $this->chompScheme($url);
        if ($this->scheme === 'file') {
            // Unescaped backslashes not used in directory separator context
            // will get caught by this, but they should have been urlencoded
            // anyway so we don't care. If this ends up being a problem, the
            // host regexp must be modified to match for backslashes when
            // the scheme is file.
            $url = str_replace('\\', '/', $url);
        }
        list($this->username, $this->password) = $this->chompLogin($url);
        $this->host = $this->chompHost($url);
        $this->port = false;
        if (preg_match('/(.*?):(.*)/', $this->host, $host_parts)) {
            if ($this->scheme === 'file' && strlen($this->host) === 2) {
                // DOS drive was placed in authority; promote it to path.
                $url = '/' . $this->host . $url;
                $this->host = false;
            } else {
                $this->host = $host_parts[1];
                $this->port = (integer)$host_parts[2];
            }
        }
        $this->path = $this->chompPath($url);
        $this->request = $this->parseRequest($this->chompRequest($url));
        $this->fragment = (strncmp($url, "#", 1) == 0 ? substr($url, 1) : false);
        $this->target = false;
    }
    
    /**
     *    Extracts the X, Y coordinate pair from an image map.
     *    @param string $url   URL so far. The coordinates will be
     *                         removed.
     *    @return array        X, Y as a pair of integers.
     *    @access private
     */
    protected function chompCoordinates(&$url) {
        if (preg_match('/(.*)\?(\d+),(\d+)$/', $url, $matches)) {
            $url = $matches[1];
            return array((integer)$matches[2], (integer)$matches[3]);
        }
        return array(false, false);
    }
    
    /**
     *    Extracts the scheme part of an incoming URL.
     *    @param string $url   URL so far. The scheme will be
     *                         removed.
     *    @return string       Scheme part or false.
     *    @access private
     */
    protected function chompScheme(&$url) {
        if (preg_match('#^([^/:]*):(//)(.*)#', $url, $matches)) {
            $url = $matches[2] . $matches[3];
            return $matches[1];
        }
        return false;
    }
    
    /**
     *    Extracts the username and password from the
     *    incoming URL. The // prefix will be reattached
     *    to the URL after the doublet is extracted.
     *    @param string $url    URL so far. The username and
     *                          password are removed.
     *    @return array         Two item list of username and
     *                          password. Will urldecode() them.
     *    @access private
     */
    protected function chompLogin(&$url) {
        $prefix = '';
        if (preg_match('#^(//)(.*)#', $url, $matches)) {
            $prefix = $matches[1];
            $url = $matches[2];
        }
        if (preg_match('#^([^/]*)@(.*)#', $url, $matches)) {
            $url = $prefix . $matches[2];
            $parts = split(":", $matches[1]);
            return array(
                    urldecode($parts[0]),
                    isset($parts[1]) ? urldecode($parts[1]) : false);
        }
        $url = $prefix . $url;
        return array(false, false);
    }
    
    /**
     *    Extracts the host part of an incoming URL.
     *    Includes the port number part. Will extract
     *    the host if it starts with // or it has
     *    a top level domain or it has at least two
     *    dots.
     *    @param string $url    URL so far. The host will be
     *                          removed.
     *    @return string        Host part guess or false.
     *    @access private
     */
    protected function chompHost(&$url) {
        if (preg_match('!^(//)(.*?)(/.*|\?.*|#.*|$)!', $url, $matches)) {
            $url = $matches[3];
            return $matches[2];
        }
        if (preg_match('!(.*?)(\.\./|\./|/|\?|#|$)(.*)!', $url, $matches)) {
            $tlds = SimpleUrl::getAllTopLevelDomains();
            if (preg_match('/[a-z0-9\-]+\.(' . $tlds . ')/i', $matches[1])) {
                $url = $matches[2] . $matches[3];
                return $matches[1];
            } elseif (preg_match('/[a-z0-9\-]+\.[a-z0-9\-]+\.[a-z0-9\-]+/i', $matches[1])) {
                $url = $matches[2] . $matches[3];
                return $matches[1];
            }
        }
        return false;
    }
    
    /**
     *    Extracts the path information from the incoming
     *    URL. Strips this path from the URL.
     *    @param string $url     URL so far. The host will be
     *                           removed.
     *    @return string         Path part or '/'.
     *    @access private
     */
    protected function chompPath(&$url) {
        if (preg_match('/(.*?)(\?|#|$)(.*)/', $url, $matches)) {
            $url = $matches[2] . $matches[3];
            return ($matches[1] ? $matches[1] : '');
        }
        return '';
    }
    
    /**
     *    Strips off the request data.
     *    @param string $url  URL so far. The request will be
     *                        removed.
     *    @return string      Raw request part.
     *    @access private
     */
    protected function chompRequest(&$url) {
        if (preg_match('/\?(.*?)(#|$)(.*)/', $url, $matches)) {
            $url = $matches[2] . $matches[3];
            return $matches[1];
        }
        return '';
    }
        
    /**
     *    Breaks the request down into an object.
     *    @param string $raw           Raw request.
     *    @return SimpleFormEncoding    Parsed data.
     *    @access private
     */
    protected function parseRequest($raw) {
        $this->raw = $raw;
        $request = new SimpleGetEncoding();
        foreach (split("&", $raw) as $pair) {
            if (preg_match('/(.*?)=(.*)/', $pair, $matches)) {
                $request->add($matches[1], urldecode($matches[2]));
            } elseif ($pair) {
                $request->add($pair, '');
            }
        }
        return $request;
    }
    
    /**
     *    Accessor for protocol part.
     *    @param string $default    Value to use if not present.
     *    @return string            Scheme name, e.g "http".
     *    @access public
     */
    function getScheme($default = false) {
        return $this->scheme ? $this->scheme : $default;
    }
    
    /**
     *    Accessor for user name.
     *    @return string    Username preceding host.
     *    @access public
     */
    function getUsername() {
        return $this->username;
    }
    
    /**
     *    Accessor for password.
     *    @return string    Password preceding host.
     *    @access public
     */
    function getPassword() {
        return $this->password;
    }
    
    /**
     *    Accessor for hostname and port.
     *    @param string $default    Value to use if not present.
     *    @return string            Hostname only.
     *    @access public
     */
    function getHost($default = false) {
        return $this->host ? $this->host : $default;
    }
    
    /**
     *    Accessor for top level domain.
     *    @return string       Last part of host.
     *    @access public
     */
    function getTld() {
        $path_parts = pathinfo($this->getHost());
        return (isset($path_parts['extension']) ? $path_parts['extension'] : false);
    }
    
    /**
     *    Accessor for port number.
     *    @return integer    TCP/IP port number.
     *    @access public
     */
    function getPort() {
        return $this->port;
    }        
            
    /**
     *    Accessor for path.
     *    @return string    Full path including leading slash if implied.
     *    @access public
     */
    function getPath() {
        if (! $this->path && $this->host) {
            return '/';
        }
        return $this->path;
    }
    
    /**
     *    Accessor for page if any. This may be a
     *    directory name if ambiguious.
     *    @return            Page name.
     *    @access public
     */
    function getPage() {
        if (! preg_match('/([^\/]*?)$/', $this->getPath(), $matches)) {
            return false;
        }
        return $matches[1];
    }
    
    /**
     *    Gets the path to the page.
     *    @return string       Path less the page.
     *    @access public
     */
    function getBasePath() {
        if (! preg_match('/(.*\/)[^\/]*?$/', $this->getPath(), $matches)) {
            return false;
        }
        return $matches[1];
    }
    
    /**
     *    Accessor for fragment at end of URL after the "#".
     *    @return string    Part after "#".
     *    @access public
     */
    function getFragment() {
        return $this->fragment;
    }
    
    /**
     *    Sets image coordinates. Set to false to clear
     *    them.
     *    @param integer $x    Horizontal position.
     *    @param integer $y    Vertical position.
     *    @access public
     */
    function setCoordinates($x = false, $y = false) {
        if (($x === false) || ($y === false)) {
            $this->x = $this->y = false;
            return;
        }
        $this->x = (integer)$x;
        $this->y = (integer)$y;
    }
    
    /**
     *    Accessor for horizontal image coordinate.
     *    @return integer        X value.
     *    @access public
     */
    function getX() {
        return $this->x;
    }
        
    /**
     *    Accessor for vertical image coordinate.
     *    @return integer        Y value.
     *    @access public
     */
    function getY() {
        return $this->y;
    }
    
    /**
     *    Accessor for current request parameters
     *    in URL string form. Will return teh original request
     *    if at all possible even if it doesn't make much
     *    sense.
     *    @return string   Form is string "?a=1&b=2", etc.
     *    @access public
     */
    function getEncodedRequest() {
        if ($this->raw) {
            $encoded = $this->raw;
        } else {
            $encoded = $this->request->asUrlRequest();
        }
        if ($encoded) {
            return '?' . preg_replace('/^\?/', '', $encoded);
        }
        return '';
    }
    
    /**
     *    Adds an additional parameter to the request.
     *    @param string $key            Name of parameter.
     *    @param string $value          Value as string.
     *    @access public
     */
    function addRequestParameter($key, $value) {
        $this->raw = false;
        $this->request->add($key, $value);
    }
    
    /**
     *    Adds additional parameters to the request.
     *    @param hash/SimpleFormEncoding $parameters   Additional
     *                                                parameters.
     *    @access public
     */
    function addRequestParameters($parameters) {
        $this->raw = false;
        $this->request->merge($parameters);
    }
    
    /**
     *    Clears down all parameters.
     *    @access public
     */
    function clearRequest() {
        $this->raw = false;
        $this->request = new SimpleGetEncoding();
    }
    
    /**
     *    Gets the frame target if present. Although
     *    not strictly part of the URL specification it
     *    acts as similarily to the browser.
     *    @return boolean/string    Frame name or false if none.
     *    @access public
     */
    function getTarget() {
        return $this->target;
    }
    
    /**
     *    Attaches a frame target.
     *    @param string $frame        Name of frame.
     *    @access public
     */
    function setTarget($frame) {
        $this->raw = false;
        $this->target = $frame;
    }
    
    /**
     *    Renders the URL back into a string.
     *    @return string        URL in canonical form.
     *    @access public
     */
    function asString() {
        $path = $this->path;
        $scheme = $identity = $host = $port = $encoded = $fragment = '';
        if ($this->username && $this->password) {
            $identity = $this->username . ':' . $this->password . '@';
        }
        if ($this->getHost()) {
            $scheme = $this->getScheme() ? $this->getScheme() : 'http';
            $scheme .= '://';
            $host = $this->getHost();
        } elseif ($this->getScheme() === 'file') {
            // Safest way; otherwise, file URLs on Windows have an extra
            // leading slash. It might be possible to convert file://
            // URIs to local file paths, but that requires more research.
            $scheme = 'file://';
        }
        if ($this->getPort() && $this->getPort() != 80 ) {
            $port = ':'.$this->getPort();
        }

        if (substr($this->path, 0, 1) == '/') {
            $path = $this->normalisePath($this->path);
        }
        $encoded = $this->getEncodedRequest();
        $fragment = $this->getFragment() ? '#'. $this->getFragment() : '';
        $coords = $this->getX() === false ? '' : '?' . $this->getX() . ',' . $this->getY();
        return "$scheme$identity$host$port$path$encoded$fragment$coords";
    }
    
    /**
     *    Replaces unknown sections to turn a relative
     *    URL into an absolute one. The base URL can
     *    be either a string or a SimpleUrl object.
     *    @param string/SimpleUrl $base       Base URL.
     *    @access public
     */
    function makeAbsolute($base) {
        if (! is_object($base)) {
            $base = new SimpleUrl($base);
        }
        if ($this->getHost()) {
            $scheme = $this->getScheme();
            $host = $this->getHost();
            $port = $this->getPort() ? ':' . $this->getPort() : '';
            $identity = $this->getIdentity() ? $this->getIdentity() . '@' : '';
            if (! $identity) {
                $identity = $base->getIdentity() ? $base->getIdentity() . '@' : '';
            }
        } else {
            $scheme = $base->getScheme();
            $host = $base->getHost();
            $port = $base->getPort() ? ':' . $base->getPort() : '';
            $identity = $base->getIdentity() ? $base->getIdentity() . '@' : '';
        }
        $path = $this->normalisePath($this->extractAbsolutePath($base));
        $encoded = $this->getEncodedRequest();
        $fragment = $this->getFragment() ? '#'. $this->getFragment() : '';
        $coords = $this->getX() === false ? '' : '?' . $this->getX() . ',' . $this->getY();
        return new SimpleUrl("$scheme://$identity$host$port$path$encoded$fragment$coords");
    }
    
    /**
     *    Replaces unknown sections of the path with base parts
     *    to return a complete absolute one.
     *    @param string/SimpleUrl $base       Base URL.
     *    @param string                       Absolute path.
     *    @access private
     */
    protected function extractAbsolutePath($base) {
        if ($this->getHost()) {
            return $this->path;
        }
        if (! $this->isRelativePath($this->path)) {
            return $this->path;
        }
        if ($this->path) {
            return $base->getBasePath() . $this->path;
        }
        return $base->getPath();
    }
    
    /**
     *    Simple test to see if a path part is relative.
     *    @param string $path        Path to test.
     *    @return boolean            True if starts with a "/".
     *    @access private
     */
    protected function isRelativePath($path) {
        return (substr($path, 0, 1) != '/');
    }
    
    /**
     *    Extracts the username and password for use in rendering
     *    a URL.
     *    @return string/boolean    Form of username:password or false.
     *    @access public
     */
    function getIdentity() {
        if ($this->username && $this->password) {
            return $this->username . ':' . $this->password;
        }
        return false;
    }
    
    /**
     *    Replaces . and .. sections of the path.
     *    @param string $path    Unoptimised path.
     *    @return string         Path with dots removed if possible.
     *    @access public
     */
    function normalisePath($path) {
        $path = preg_replace('|/\./|', '/', $path);
        return preg_replace('|/[^/]+/\.\./|', '/', $path);
    }
    
    /**
     *    A pipe seperated list of all TLDs that result in two part
     *    domain names.
     *    @return string        Pipe separated list.
     *    @access public
     */
    static function getAllTopLevelDomains() {
        return 'com|edu|net|org|gov|mil|int|biz|info|name|pro|aero|coop|museum';
    }
}
?>