<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * DomainKey Signer used to apply DomainKeys Signature to a message.
 *
 * @author Xavier De Cock <xdecock@gmail.com>
 */
class Swift_Signers_DomainKeySigner implements Swift_Signers_HeaderSigner
{
    /**
     * PrivateKey.
     *
     * @var string
     */
    protected $_privateKey;

    /**
     * DomainName.
     *
     * @var string
     */
    protected $_domainName;

    /**
     * Selector.
     *
     * @var string
     */
    protected $_selector;

    /**
     * Hash algorithm used.
     *
     * @var string
     */
    protected $_hashAlgorithm = 'rsa-sha1';

    /**
     * Canonisation method.
     *
     * @var string
     */
    protected $_canon = 'simple';

    /**
     * Headers not being signed.
     *
     * @var array
     */
    protected $_ignoredHeaders = array();

    /**
     * Signer identity.
     *
     * @var string
     */
    protected $_signerIdentity;

    /**
     * Must we embed signed headers?
     *
     * @var bool
     */
    protected $_debugHeaders = false;

    // work variables
    /**
     * Headers used to generate hash.
     *
     * @var array
     */
    private $_signedHeaders = array();

    /**
     * Stores the signature header.
     *
     * @var Swift_Mime_Headers_ParameterizedHeader
     */
    protected $_domainKeyHeader;

    /**
     * Hash Handler.
     *
     * @var resource|null
     */
    private $_hashHandler;

    private $_hash;

    private $_canonData = '';

    private $_bodyCanonEmptyCounter = 0;

    private $_bodyCanonIgnoreStart = 2;

    private $_bodyCanonSpace = false;

    private $_bodyCanonLastChar = null;

    private $_bodyCanonLine = '';

    private $_bound = array();

    /**
     * Constructor.
     *
     * @param string $privateKey
     * @param string $domainName
     * @param string $selector
     */
    public function __construct($privateKey, $domainName, $selector)
    {
        $this->_privateKey = $privateKey;
        $this->_domainName = $domainName;
        $this->_signerIdentity = '@'.$domainName;
        $this->_selector = $selector;
    }

    /**
     * Instanciate DomainKeySigner.
     *
     * @param string $privateKey
     * @param string $domainName
     * @param string $selector
     *
     * @return Swift_Signers_DomainKeySigner
     */
    public static function newInstance($privateKey, $domainName, $selector)
    {
        return new static($privateKey, $domainName, $selector);
    }

    /**
     * Resets internal states.
     *
     * @return Swift_Signers_DomainKeysSigner
     */
    public function reset()
    {
        $this->_hash = null;
        $this->_hashHandler = null;
        $this->_bodyCanonIgnoreStart = 2;
        $this->_bodyCanonEmptyCounter = 0;
        $this->_bodyCanonLastChar = null;
        $this->_bodyCanonSpace = false;

        return $this;
    }

    /**
     * Writes $bytes to the end of the stream.
     *
     * Writing may not happen immediately if the stream chooses to buffer.  If
     * you want to write these bytes with immediate effect, call {@link commit()}
     * after calling write().
     *
     * This method returns the sequence ID of the write (i.e. 1 for first, 2 for
     * second, etc etc).
     *
     * @param string $bytes
     *
     * @throws Swift_IoException
     *
     * @return int
     * @return Swift_Signers_DomainKeysSigner
     */
    public function write($bytes)
    {
        $this->_canonicalizeBody($bytes);
        foreach ($this->_bound as $is) {
            $is->write($bytes);
        }

        return $this;
    }

    /**
     * For any bytes that are currently buffered inside the stream, force them
     * off the buffer.
     *
     * @throws Swift_IoException
     *
     * @return Swift_Signers_DomainKeysSigner
     */
    public function commit()
    {
        // Nothing to do
        return $this;
    }

    /**
     * Attach $is to this stream.
     * The stream acts as an observer, receiving all data that is written.
     * All {@link write()} and {@link flushBuffers()} operations will be mirrored.
     *
     * @param Swift_InputByteStream $is
     *
     * @return Swift_Signers_DomainKeysSigner
     */
    public function bind(Swift_InputByteStream $is)
    {
        // Don't have to mirror anything
        $this->_bound[] = $is;

        return $this;
    }

    /**
     * Remove an already bound stream.
     * If $is is not bound, no errors will be raised.
     * If the stream currently has any buffered data it will be written to $is
     * before unbinding occurs.
     *
     * @param Swift_InputByteStream $is
     *
     * @return Swift_Signers_DomainKeysSigner
     */
    public function unbind(Swift_InputByteStream $is)
    {
        // Don't have to mirror anything
        foreach ($this->_bound as $k => $stream) {
            if ($stream === $is) {
                unset($this->_bound[$k]);

                return;
            }
        }

        return $this;
    }

    /**
     * Flush the contents of the stream (empty it) and set the internal pointer
     * to the beginning.
     *
     * @throws Swift_IoException
     *
     * @return Swift_Signers_DomainKeysSigner
     */
    public function flushBuffers()
    {
        $this->reset();

        return $this;
    }

    /**
     * Set hash_algorithm, must be one of rsa-sha256 | rsa-sha1 defaults to rsa-sha256.
     *
     * @param string $hash
     *
     * @return Swift_Signers_DomainKeysSigner
     */
    public function setHashAlgorithm($hash)
    {
        $this->_hashAlgorithm = 'rsa-sha1';

        return $this;
    }

    /**
     * Set the canonicalization algorithm.
     *
     * @param string $canon simple | nofws defaults to simple
     *
     * @return Swift_Signers_DomainKeysSigner
     */
    public function setCanon($canon)
    {
        if ($canon == 'nofws') {
            $this->_canon = 'nofws';
        } else {
            $this->_canon = 'simple';
        }

        return $this;
    }

    /**
     * Set the signer identity.
     *
     * @param string $identity
     *
     * @return Swift_Signers_DomainKeySigner
     */
    public function setSignerIdentity($identity)
    {
        $this->_signerIdentity = $identity;

        return $this;
    }

    /**
     * Enable / disable the DebugHeaders.
     *
     * @param bool $debug
     *
     * @return Swift_Signers_DomainKeySigner
     */
    public function setDebugHeaders($debug)
    {
        $this->_debugHeaders = (bool) $debug;

        return $this;
    }

    /**
     * Start Body.
     */
    public function startBody()
    {
    }

    /**
     * End Body.
     */
    public function endBody()
    {
        $this->_endOfBody();
    }

    /**
     * Returns the list of Headers Tampered by this plugin.
     *
     * @return array
     */
    public function getAlteredHeaders()
    {
        if ($this->_debugHeaders) {
            return array('DomainKey-Signature', 'X-DebugHash');
        } else {
            return array('DomainKey-Signature');
        }
    }

    /**
     * Adds an ignored Header.
     *
     * @param string $header_name
     *
     * @return Swift_Signers_DomainKeySigner
     */
    public function ignoreHeader($header_name)
    {
        $this->_ignoredHeaders[strtolower($header_name)] = true;

        return $this;
    }

    /**
     * Set the headers to sign.
     *
     * @param Swift_Mime_HeaderSet $headers
     *
     * @return Swift_Signers_DomainKeySigner
     */
    public function setHeaders(Swift_Mime_HeaderSet $headers)
    {
        $this->_startHash();
        $this->_canonData = '';
        // Loop through Headers
        $listHeaders = $headers->listAll();
        foreach ($listHeaders as $hName) {
            // Check if we need to ignore Header
            if (!isset($this->_ignoredHeaders[strtolower($hName)])) {
                if ($headers->has($hName)) {
                    $tmp = $headers->getAll($hName);
                    foreach ($tmp as $header) {
                        if ($header->getFieldBody() != '') {
                            $this->_addHeader($header->toString());
                            $this->_signedHeaders[] = $header->getFieldName();
                        }
                    }
                }
            }
        }
        $this->_endOfHeaders();

        return $this;
    }

    /**
     * Add the signature to the given Headers.
     *
     * @param Swift_Mime_HeaderSet $headers
     *
     * @return Swift_Signers_DomainKeySigner
     */
    public function addSignature(Swift_Mime_HeaderSet $headers)
    {
        // Prepare the DomainKey-Signature Header
        $params = array('a' => $this->_hashAlgorithm, 'b' => chunk_split(base64_encode($this->_getEncryptedHash()), 73, ' '), 'c' => $this->_canon, 'd' => $this->_domainName, 'h' => implode(': ', $this->_signedHeaders), 'q' => 'dns', 's' => $this->_selector);
        $string = '';
        foreach ($params as $k => $v) {
            $string .= $k.'='.$v.'; ';
        }
        $string = trim($string);
        $headers->addTextHeader('DomainKey-Signature', $string);

        return $this;
    }

    /* Private helpers */

    protected function _addHeader($header)
    {
        switch ($this->_canon) {
            case 'nofws' :
                // Prepare Header and cascade
                $exploded = explode(':', $header, 2);
                $name = strtolower(trim($exploded[0]));
                $value = str_replace("\r\n", '', $exploded[1]);
                $value = preg_replace("/[ \t][ \t]+/", ' ', $value);
                $header = $name.':'.trim($value)."\r\n";
            case 'simple' :
                // Nothing to do
        }
        $this->_addToHash($header);
    }

    protected function _endOfHeaders()
    {
        $this->_bodyCanonEmptyCounter = 1;
    }

    protected function _canonicalizeBody($string)
    {
        $len = strlen($string);
        $canon = '';
        $nofws = ($this->_canon == 'nofws');
        for ($i = 0; $i < $len; ++$i) {
            if ($this->_bodyCanonIgnoreStart > 0) {
                --$this->_bodyCanonIgnoreStart;
                continue;
            }
            switch ($string[$i]) {
                case "\r" :
                    $this->_bodyCanonLastChar = "\r";
                    break;
                case "\n" :
                    if ($this->_bodyCanonLastChar == "\r") {
                        if ($nofws) {
                            $this->_bodyCanonSpace = false;
                        }
                        if ($this->_bodyCanonLine == '') {
                            ++$this->_bodyCanonEmptyCounter;
                        } else {
                            $this->_bodyCanonLine = '';
                            $canon .= "\r\n";
                        }
                    } else {
                        // Wooops Error
                        throw new Swift_SwiftException('Invalid new line sequence in mail found \n without preceding \r');
                    }
                    break;
                case ' ' :
                case "\t" :
                case "\x09": //HTAB
                    if ($nofws) {
                        $this->_bodyCanonSpace = true;
                        break;
                    }
                default :
                    if ($this->_bodyCanonEmptyCounter > 0) {
                        $canon .= str_repeat("\r\n", $this->_bodyCanonEmptyCounter);
                        $this->_bodyCanonEmptyCounter = 0;
                    }
                    $this->_bodyCanonLine .= $string[$i];
                    $canon .= $string[$i];
            }
        }
        $this->_addToHash($canon);
    }

    protected function _endOfBody()
    {
        if (strlen($this->_bodyCanonLine) > 0) {
            $this->_addToHash("\r\n");
        }
        $this->_hash = hash_final($this->_hashHandler, true);
    }

    private function _addToHash($string)
    {
        $this->_canonData .= $string;
        hash_update($this->_hashHandler, $string);
    }

    private function _startHash()
    {
        // Init
        switch ($this->_hashAlgorithm) {
            case 'rsa-sha1' :
                $this->_hashHandler = hash_init('sha1');
                break;
        }
        $this->_canonLine = '';
    }

    /**
     * @throws Swift_SwiftException
     *
     * @return string
     */
    private function _getEncryptedHash()
    {
        $signature = '';
        $pkeyId = openssl_get_privatekey($this->_privateKey);
        if (!$pkeyId) {
            throw new Swift_SwiftException('Unable to load DomainKey Private Key ['.openssl_error_string().']');
        }
        if (openssl_sign($this->_canonData, $signature, $pkeyId, OPENSSL_ALGO_SHA1)) {
            return $signature;
        }
        throw new Swift_SwiftException('Unable to sign DomainKey Hash  ['.openssl_error_string().']');
    }
}
