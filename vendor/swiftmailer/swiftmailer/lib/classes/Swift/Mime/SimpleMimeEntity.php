<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * A MIME entity, in a multipart message.
 *
 * @author Chris Corbyn
 */
class Swift_Mime_SimpleMimeEntity implements Swift_Mime_MimeEntity
{
    /** A collection of Headers for this mime entity */
    private $_headers;

    /** The body as a string, or a stream */
    private $_body;

    /** The encoder that encodes the body into a streamable format */
    private $_encoder;

    /** The grammar to use for id validation */
    private $_grammar;

    /** A mime boundary, if any is used */
    private $_boundary;

    /** Mime types to be used based on the nesting level */
    private $_compositeRanges = array(
        'multipart/mixed' => array(self::LEVEL_TOP, self::LEVEL_MIXED),
        'multipart/alternative' => array(self::LEVEL_MIXED, self::LEVEL_ALTERNATIVE),
        'multipart/related' => array(self::LEVEL_ALTERNATIVE, self::LEVEL_RELATED),
    );

    /** A set of filter rules to define what level an entity should be nested at */
    private $_compoundLevelFilters = array();

    /** The nesting level of this entity */
    private $_nestingLevel = self::LEVEL_ALTERNATIVE;

    /** A KeyCache instance used during encoding and streaming */
    private $_cache;

    /** Direct descendants of this entity */
    private $_immediateChildren = array();

    /** All descendants of this entity */
    private $_children = array();

    /** The maximum line length of the body of this entity */
    private $_maxLineLength = 78;

    /** The order in which alternative mime types should appear */
    private $_alternativePartOrder = array(
        'text/plain' => 1,
        'text/html' => 2,
        'multipart/related' => 3,
    );

    /** The CID of this entity */
    private $_id;

    /** The key used for accessing the cache */
    private $_cacheKey;

    protected $_userContentType;

    /**
     * Create a new SimpleMimeEntity with $headers, $encoder and $cache.
     *
     * @param Swift_Mime_HeaderSet      $headers
     * @param Swift_Mime_ContentEncoder $encoder
     * @param Swift_KeyCache            $cache
     * @param Swift_Mime_Grammar        $grammar
     */
    public function __construct(Swift_Mime_HeaderSet $headers, Swift_Mime_ContentEncoder $encoder, Swift_KeyCache $cache, Swift_Mime_Grammar $grammar)
    {
        $this->_cacheKey = md5(uniqid(getmypid().mt_rand(), true));
        $this->_cache = $cache;
        $this->_headers = $headers;
        $this->_grammar = $grammar;
        $this->setEncoder($encoder);
        $this->_headers->defineOrdering(array('Content-Type', 'Content-Transfer-Encoding'));

        // This array specifies that, when the entire MIME document contains
        // $compoundLevel, then for each child within $level, if its Content-Type
        // is $contentType then it should be treated as if it's level is
        // $neededLevel instead.  I tried to write that unambiguously! :-\
        // Data Structure:
        // array (
        //   $compoundLevel => array(
        //     $level => array(
        //       $contentType => $neededLevel
        //     )
        //   )
        // )

        $this->_compoundLevelFilters = array(
            (self::LEVEL_ALTERNATIVE + self::LEVEL_RELATED) => array(
                self::LEVEL_ALTERNATIVE => array(
                    'text/plain' => self::LEVEL_ALTERNATIVE,
                    'text/html' => self::LEVEL_RELATED,
                    ),
                ),
            );

        $this->_id = $this->getRandomId();
    }

    /**
     * Generate a new Content-ID or Message-ID for this MIME entity.
     *
     * @return string
     */
    public function generateId()
    {
        $this->setId($this->getRandomId());

        return $this->_id;
    }

    /**
     * Get the {@link Swift_Mime_HeaderSet} for this entity.
     *
     * @return Swift_Mime_HeaderSet
     */
    public function getHeaders()
    {
        return $this->_headers;
    }

    /**
     * Get the nesting level of this entity.
     *
     * @see LEVEL_TOP, LEVEL_MIXED, LEVEL_RELATED, LEVEL_ALTERNATIVE
     *
     * @return int
     */
    public function getNestingLevel()
    {
        return $this->_nestingLevel;
    }

    /**
     * Get the Content-type of this entity.
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->_getHeaderFieldModel('Content-Type');
    }

    /**
     * Set the Content-type of this entity.
     *
     * @param string $type
     *
     * @return Swift_Mime_SimpleMimeEntity
     */
    public function setContentType($type)
    {
        $this->_setContentTypeInHeaders($type);
        // Keep track of the value so that if the content-type changes automatically
        // due to added child entities, it can be restored if they are later removed
        $this->_userContentType = $type;

        return $this;
    }

    /**
     * Get the CID of this entity.
     *
     * The CID will only be present in headers if a Content-ID header is present.
     *
     * @return string
     */
    public function getId()
    {
        $tmp = (array) $this->_getHeaderFieldModel($this->_getIdField());

        return $this->_headers->has($this->_getIdField()) ? current($tmp) : $this->_id;
    }

    /**
     * Set the CID of this entity.
     *
     * @param string $id
     *
     * @return Swift_Mime_SimpleMimeEntity
     */
    public function setId($id)
    {
        if (!$this->_setHeaderFieldModel($this->_getIdField(), $id)) {
            $this->_headers->addIdHeader($this->_getIdField(), $id);
        }
        $this->_id = $id;

        return $this;
    }

    /**
     * Get the description of this entity.
     *
     * This value comes from the Content-Description header if set.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->_getHeaderFieldModel('Content-Description');
    }

    /**
     * Set the description of this entity.
     *
     * This method sets a value in the Content-ID header.
     *
     * @param string $description
     *
     * @return Swift_Mime_SimpleMimeEntity
     */
    public function setDescription($description)
    {
        if (!$this->_setHeaderFieldModel('Content-Description', $description)) {
            $this->_headers->addTextHeader('Content-Description', $description);
        }

        return $this;
    }

    /**
     * Get the maximum line length of the body of this entity.
     *
     * @return int
     */
    public function getMaxLineLength()
    {
        return $this->_maxLineLength;
    }

    /**
     * Set the maximum line length of lines in this body.
     *
     * Though not enforced by the library, lines should not exceed 1000 chars.
     *
     * @param int $length
     *
     * @return Swift_Mime_SimpleMimeEntity
     */
    public function setMaxLineLength($length)
    {
        $this->_maxLineLength = $length;

        return $this;
    }

    /**
     * Get all children added to this entity.
     *
     * @return Swift_Mime_MimeEntity[]
     */
    public function getChildren()
    {
        return $this->_children;
    }

    /**
     * Set all children of this entity.
     *
     * @param Swift_Mime_MimeEntity[] $children
     * @param int                     $compoundLevel For internal use only
     *
     * @return Swift_Mime_SimpleMimeEntity
     */
    public function setChildren(array $children, $compoundLevel = null)
    {
        // TODO: Try to refactor this logic

        $compoundLevel = isset($compoundLevel)
            ? $compoundLevel
            : $this->_getCompoundLevel($children)
            ;

        $immediateChildren = array();
        $grandchildren = array();
        $newContentType = $this->_userContentType;

        foreach ($children as $child) {
            $level = $this->_getNeededChildLevel($child, $compoundLevel);
            if (empty($immediateChildren)) {
                //first iteration
                $immediateChildren = array($child);
            } else {
                $nextLevel = $this->_getNeededChildLevel($immediateChildren[0], $compoundLevel);
                if ($nextLevel == $level) {
                    $immediateChildren[] = $child;
                } elseif ($level < $nextLevel) {
                    // Re-assign immediateChildren to grandchildren
                    $grandchildren = array_merge($grandchildren, $immediateChildren);
                    // Set new children
                    $immediateChildren = array($child);
                } else {
                    $grandchildren[] = $child;
                }
            }
        }

        if (!empty($immediateChildren)) {
            $lowestLevel = $this->_getNeededChildLevel($immediateChildren[0], $compoundLevel);

            // Determine which composite media type is needed to accommodate the
            // immediate children
            foreach ($this->_compositeRanges as $mediaType => $range) {
                if ($lowestLevel > $range[0]
                    && $lowestLevel <= $range[1]) {
                    $newContentType = $mediaType;
                    break;
                }
            }

            // Put any grandchildren in a subpart
            if (!empty($grandchildren)) {
                $subentity = $this->_createChild();
                $subentity->_setNestingLevel($lowestLevel);
                $subentity->setChildren($grandchildren, $compoundLevel);
                array_unshift($immediateChildren, $subentity);
            }
        }

        $this->_immediateChildren = $immediateChildren;
        $this->_children = $children;
        $this->_setContentTypeInHeaders($newContentType);
        $this->_fixHeaders();
        $this->_sortChildren();

        return $this;
    }

    /**
     * Get the body of this entity as a string.
     *
     * @return string
     */
    public function getBody()
    {
        return ($this->_body instanceof Swift_OutputByteStream)
            ? $this->_readStream($this->_body)
            : $this->_body;
    }

    /**
     * Set the body of this entity, either as a string, or as an instance of
     * {@link Swift_OutputByteStream}.
     *
     * @param mixed  $body
     * @param string $contentType optional
     *
     * @return Swift_Mime_SimpleMimeEntity
     */
    public function setBody($body, $contentType = null)
    {
        if ($body !== $this->_body) {
            $this->_clearCache();
        }

        $this->_body = $body;
        if (isset($contentType)) {
            $this->setContentType($contentType);
        }

        return $this;
    }

    /**
     * Get the encoder used for the body of this entity.
     *
     * @return Swift_Mime_ContentEncoder
     */
    public function getEncoder()
    {
        return $this->_encoder;
    }

    /**
     * Set the encoder used for the body of this entity.
     *
     * @param Swift_Mime_ContentEncoder $encoder
     *
     * @return Swift_Mime_SimpleMimeEntity
     */
    public function setEncoder(Swift_Mime_ContentEncoder $encoder)
    {
        if ($encoder !== $this->_encoder) {
            $this->_clearCache();
        }

        $this->_encoder = $encoder;
        $this->_setEncoding($encoder->getName());
        $this->_notifyEncoderChanged($encoder);

        return $this;
    }

    /**
     * Get the boundary used to separate children in this entity.
     *
     * @return string
     */
    public function getBoundary()
    {
        if (!isset($this->_boundary)) {
            $this->_boundary = '_=_swift_v4_'.time().'_'.md5(getmypid().mt_rand().uniqid('', true)).'_=_';
        }

        return $this->_boundary;
    }

    /**
     * Set the boundary used to separate children in this entity.
     *
     * @param string $boundary
     *
     * @throws Swift_RfcComplianceException
     *
     * @return Swift_Mime_SimpleMimeEntity
     */
    public function setBoundary($boundary)
    {
        $this->_assertValidBoundary($boundary);
        $this->_boundary = $boundary;

        return $this;
    }

    /**
     * Receive notification that the charset of this entity, or a parent entity
     * has changed.
     *
     * @param string $charset
     */
    public function charsetChanged($charset)
    {
        $this->_notifyCharsetChanged($charset);
    }

    /**
     * Receive notification that the encoder of this entity or a parent entity
     * has changed.
     *
     * @param Swift_Mime_ContentEncoder $encoder
     */
    public function encoderChanged(Swift_Mime_ContentEncoder $encoder)
    {
        $this->_notifyEncoderChanged($encoder);
    }

    /**
     * Get this entire entity as a string.
     *
     * @return string
     */
    public function toString()
    {
        $string = $this->_headers->toString();
        $string .= $this->_bodyToString();

        return $string;
    }

    /**
     * Get this entire entity as a string.
     *
     * @return string
     */
    protected function _bodyToString()
    {
        $string = '';

        if (isset($this->_body) && empty($this->_immediateChildren)) {
            if ($this->_cache->hasKey($this->_cacheKey, 'body')) {
                $body = $this->_cache->getString($this->_cacheKey, 'body');
            } else {
                $body = "\r\n".$this->_encoder->encodeString($this->getBody(), 0,
                    $this->getMaxLineLength()
                    );
                $this->_cache->setString($this->_cacheKey, 'body', $body,
                    Swift_KeyCache::MODE_WRITE
                    );
            }
            $string .= $body;
        }

        if (!empty($this->_immediateChildren)) {
            foreach ($this->_immediateChildren as $child) {
                $string .= "\r\n\r\n--".$this->getBoundary()."\r\n";
                $string .= $child->toString();
            }
            $string .= "\r\n\r\n--".$this->getBoundary()."--\r\n";
        }

        return $string;
    }

    /**
     * Returns a string representation of this object.
     *
     * @see toString()
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Write this entire entity to a {@see Swift_InputByteStream}.
     *
     * @param Swift_InputByteStream
     */
    public function toByteStream(Swift_InputByteStream $is)
    {
        $is->write($this->_headers->toString());
        $is->commit();

        $this->_bodyToByteStream($is);
    }

    /**
     * Write this entire entity to a {@link Swift_InputByteStream}.
     *
     * @param Swift_InputByteStream
     */
    protected function _bodyToByteStream(Swift_InputByteStream $is)
    {
        if (empty($this->_immediateChildren)) {
            if (isset($this->_body)) {
                if ($this->_cache->hasKey($this->_cacheKey, 'body')) {
                    $this->_cache->exportToByteStream($this->_cacheKey, 'body', $is);
                } else {
                    $cacheIs = $this->_cache->getInputByteStream($this->_cacheKey, 'body');
                    if ($cacheIs) {
                        $is->bind($cacheIs);
                    }

                    $is->write("\r\n");

                    if ($this->_body instanceof Swift_OutputByteStream) {
                        $this->_body->setReadPointer(0);

                        $this->_encoder->encodeByteStream($this->_body, $is, 0, $this->getMaxLineLength());
                    } else {
                        $is->write($this->_encoder->encodeString($this->getBody(), 0, $this->getMaxLineLength()));
                    }

                    if ($cacheIs) {
                        $is->unbind($cacheIs);
                    }
                }
            }
        }

        if (!empty($this->_immediateChildren)) {
            foreach ($this->_immediateChildren as $child) {
                $is->write("\r\n\r\n--".$this->getBoundary()."\r\n");
                $child->toByteStream($is);
            }
            $is->write("\r\n\r\n--".$this->getBoundary()."--\r\n");
        }
    }

    /**
     * Get the name of the header that provides the ID of this entity.
     */
    protected function _getIdField()
    {
        return 'Content-ID';
    }

    /**
     * Get the model data (usually an array or a string) for $field.
     */
    protected function _getHeaderFieldModel($field)
    {
        if ($this->_headers->has($field)) {
            return $this->_headers->get($field)->getFieldBodyModel();
        }
    }

    /**
     * Set the model data for $field.
     */
    protected function _setHeaderFieldModel($field, $model)
    {
        if ($this->_headers->has($field)) {
            $this->_headers->get($field)->setFieldBodyModel($model);

            return true;
        } else {
            return false;
        }
    }

    /**
     * Get the parameter value of $parameter on $field header.
     */
    protected function _getHeaderParameter($field, $parameter)
    {
        if ($this->_headers->has($field)) {
            return $this->_headers->get($field)->getParameter($parameter);
        }
    }

    /**
     * Set the parameter value of $parameter on $field header.
     */
    protected function _setHeaderParameter($field, $parameter, $value)
    {
        if ($this->_headers->has($field)) {
            $this->_headers->get($field)->setParameter($parameter, $value);

            return true;
        } else {
            return false;
        }
    }

    /**
     * Re-evaluate what content type and encoding should be used on this entity.
     */
    protected function _fixHeaders()
    {
        if (count($this->_immediateChildren)) {
            $this->_setHeaderParameter('Content-Type', 'boundary',
                $this->getBoundary()
                );
            $this->_headers->remove('Content-Transfer-Encoding');
        } else {
            $this->_setHeaderParameter('Content-Type', 'boundary', null);
            $this->_setEncoding($this->_encoder->getName());
        }
    }

    /**
     * Get the KeyCache used in this entity.
     *
     * @return Swift_KeyCache
     */
    protected function _getCache()
    {
        return $this->_cache;
    }

    /**
     * Get the grammar used for validation.
     *
     * @return Swift_Mime_Grammar
     */
    protected function _getGrammar()
    {
        return $this->_grammar;
    }

    /**
     * Empty the KeyCache for this entity.
     */
    protected function _clearCache()
    {
        $this->_cache->clearKey($this->_cacheKey, 'body');
    }

    /**
     * Returns a random Content-ID or Message-ID.
     *
     * @return string
     */
    protected function getRandomId()
    {
        $idLeft = md5(getmypid().'.'.time().'.'.uniqid(mt_rand(), true));
        $idRight = !empty($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'swift.generated';
        $id = $idLeft.'@'.$idRight;

        try {
            $this->_assertValidId($id);
        } catch (Swift_RfcComplianceException $e) {
            $id = $idLeft.'@swift.generated';
        }

        return $id;
    }

    private function _readStream(Swift_OutputByteStream $os)
    {
        $string = '';
        while (false !== $bytes = $os->read(8192)) {
            $string .= $bytes;
        }

        $os->setReadPointer(0);

        return $string;
    }

    private function _setEncoding($encoding)
    {
        if (!$this->_setHeaderFieldModel('Content-Transfer-Encoding', $encoding)) {
            $this->_headers->addTextHeader('Content-Transfer-Encoding', $encoding);
        }
    }

    private function _assertValidBoundary($boundary)
    {
        if (!preg_match(
            '/^[a-z0-9\'\(\)\+_\-,\.\/:=\?\ ]{0,69}[a-z0-9\'\(\)\+_\-,\.\/:=\?]$/Di',
            $boundary)) {
            throw new Swift_RfcComplianceException('Mime boundary set is not RFC 2046 compliant.');
        }
    }

    private function _setContentTypeInHeaders($type)
    {
        if (!$this->_setHeaderFieldModel('Content-Type', $type)) {
            $this->_headers->addParameterizedHeader('Content-Type', $type);
        }
    }

    private function _setNestingLevel($level)
    {
        $this->_nestingLevel = $level;
    }

    private function _getCompoundLevel($children)
    {
        $level = 0;
        foreach ($children as $child) {
            $level |= $child->getNestingLevel();
        }

        return $level;
    }

    private function _getNeededChildLevel($child, $compoundLevel)
    {
        $filter = array();
        foreach ($this->_compoundLevelFilters as $bitmask => $rules) {
            if (($compoundLevel & $bitmask) === $bitmask) {
                $filter = $rules + $filter;
            }
        }

        $realLevel = $child->getNestingLevel();
        $lowercaseType = strtolower($child->getContentType());

        if (isset($filter[$realLevel])
            && isset($filter[$realLevel][$lowercaseType])) {
            return $filter[$realLevel][$lowercaseType];
        } else {
            return $realLevel;
        }
    }

    private function _createChild()
    {
        return new self($this->_headers->newInstance(),
            $this->_encoder, $this->_cache, $this->_grammar);
    }

    private function _notifyEncoderChanged(Swift_Mime_ContentEncoder $encoder)
    {
        foreach ($this->_immediateChildren as $child) {
            $child->encoderChanged($encoder);
        }
    }

    private function _notifyCharsetChanged($charset)
    {
        $this->_encoder->charsetChanged($charset);
        $this->_headers->charsetChanged($charset);
        foreach ($this->_immediateChildren as $child) {
            $child->charsetChanged($charset);
        }
    }

    private function _sortChildren()
    {
        $shouldSort = false;
        foreach ($this->_immediateChildren as $child) {
            // NOTE: This include alternative parts moved into a related part
            if ($child->getNestingLevel() == self::LEVEL_ALTERNATIVE) {
                $shouldSort = true;
                break;
            }
        }

        // Sort in order of preference, if there is one
        if ($shouldSort) {
            usort($this->_immediateChildren, array($this, '_childSortAlgorithm'));
        }
    }

    private function _childSortAlgorithm($a, $b)
    {
        $typePrefs = array();
        $types = array(
            strtolower($a->getContentType()),
            strtolower($b->getContentType()),
            );
        foreach ($types as $type) {
            $typePrefs[] = (array_key_exists($type, $this->_alternativePartOrder))
                ? $this->_alternativePartOrder[$type]
                : (max($this->_alternativePartOrder) + 1);
        }

        return ($typePrefs[0] >= $typePrefs[1]) ? 1 : -1;
    }

    // -- Destructor

    /**
     * Empties it's own contents from the cache.
     */
    public function __destruct()
    {
        $this->_cache->clearAll($this->_cacheKey);
    }

    /**
     * Throws an Exception if the id passed does not comply with RFC 2822.
     *
     * @param string $id
     *
     * @throws Swift_RfcComplianceException
     */
    private function _assertValidId($id)
    {
        if (!preg_match(
            '/^'.$this->_grammar->getDefinition('id-left').'@'.
            $this->_grammar->getDefinition('id-right').'$/D',
            $id
            )) {
            throw new Swift_RfcComplianceException(
                'Invalid ID given <'.$id.'>'
                );
        }
    }

    /**
     * Make a deep copy of object.
     */
    public function __clone()
    {
        $this->_headers = clone $this->_headers;
        $this->_encoder = clone $this->_encoder;
        $this->_cacheKey = uniqid();
        $children = array();
        foreach ($this->_children as $pos => $child) {
            $children[$pos] = clone $child;
        }
        $this->setChildren($children);
    }
}
