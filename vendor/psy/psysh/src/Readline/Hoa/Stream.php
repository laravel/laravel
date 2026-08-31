<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2017, Hoa community. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Hoa nor the names of its contributors may be
 *       used to endorse or promote products derived from this software without
 *       specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS AND CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

namespace Psy\Readline\Hoa;

/**
 * Class \Hoa\Stream.
 *
 * Static register for all streams (files, sockets etc.).
 */
abstract class Stream implements IStream, EventListenable
{
    use EventListens;

    /**
     * Name index in the stream bucket.
     */
    const NAME = 0;

    /**
     * Handler index in the stream bucket.
     */
    const HANDLER = 1;

    /**
     * Resource index in the stream bucket.
     */
    const RESOURCE = 2;

    /**
     * Context index in the stream bucket.
     */
    const CONTEXT = 3;

    /**
     * Default buffer size.
     */
    const DEFAULT_BUFFER_SIZE = 8192;

    /**
     * Current stream bucket.
     */
    protected $_bucket = [];

    /**
     * Static stream register.
     */
    private static $_register = [];

    /**
     * Buffer size (default is 8Ko).
     */
    protected $_bufferSize = self::DEFAULT_BUFFER_SIZE;

    /**
     * Original stream name, given to the stream constructor.
     */
    protected $_streamName = null;

    /**
     * Context name.
     */
    protected $_context = null;

    /**
     * Whether the opening has been deferred.
     */
    protected $_hasBeenDeferred = false;

    /**
     * Whether this stream is already opened by another handler.
     */
    protected $_borrowing = false;

    /**
     * Set the current stream.
     * If not exists in the register, try to call the
     * `$this->_open()` method. Please, see the `self::_getStream()` method.
     */
    public function __construct(string $streamName, ?string $context = null, bool $wait = false)
    {
        $this->_streamName = $streamName;
        $this->_context = $context;
        $this->_hasBeenDeferred = $wait;
        $this->setListener(
            new EventListener(
                $this,
                [
                    'authrequire',
                    'authresult',
                    'complete',
                    'connect',
                    'failure',
                    'mimetype',
                    'progress',
                    'redirect',
                    'resolve',
                    'size',
                ]
            )
        );

        if (true === $wait) {
            return;
        }

        $this->open();

        return;
    }

    /**
     * Get a stream in the register.
     * If the stream does not exist, try to open it by calling the
     * $handler->_open() method.
     */
    private static function &_getStream(
        string $streamName,
        self $handler,
        ?string $context = null
    ): array {
        $name = \md5($streamName);

        if (null !== $context) {
            if (false === StreamContext::contextExists($context)) {
                throw new StreamException('Context %s was not previously declared, cannot retrieve '.'this context.', 0, $context);
            }

            $context = StreamContext::getInstance($context);
        }

        if (!isset(self::$_register[$name])) {
            self::$_register[$name] = [
                self::NAME     => $streamName,
                self::HANDLER  => $handler,
                self::RESOURCE => $handler->_open($streamName, $context),
                self::CONTEXT  => $context,
            ];
            Event::register(
                'hoa://Event/Stream/'.$streamName,
                $handler
            );
            // Add :open-ready?
            Event::register(
                'hoa://Event/Stream/'.$streamName.':close-before',
                $handler
            );
        } else {
            $handler->_borrowing = true;
        }

        if (null === self::$_register[$name][self::RESOURCE]) {
            self::$_register[$name][self::RESOURCE]
                = $handler->_open($streamName, $context);
        }

        return self::$_register[$name];
    }

    /**
     * Open the stream and return the associated resource.
     * Note: This method is protected, but do not forget that it could be
     * overloaded into a public context.
     */
    abstract protected function &_open(string $streamName, ?StreamContext $context = null);

    /**
     * Close the current stream.
     * Note: this method is protected, but do not forget that it could be
     * overloaded into a public context.
     */
    abstract protected function _close(): bool;

    /**
     * Open the stream.
     */
    final public function open(): self
    {
        $context = $this->_context;

        if (true === $this->hasBeenDeferred()) {
            if (null === $context) {
                $handle = StreamContext::getInstance(\uniqid());
                $handle->setParameters([
                    'notification' => [$this, '_notify'],
                ]);
                $context = $handle->getId();
            } elseif (true === StreamContext::contextExists($context)) {
                $handle = StreamContext::getInstance($context);
                $parameters = $handle->getParameters();

                if (!isset($parameters['notification'])) {
                    $handle->setParameters([
                        'notification' => [$this, '_notify'],
                    ]);
                }
            }
        }

        $this->_bufferSize = self::DEFAULT_BUFFER_SIZE;
        $this->_bucket = self::_getStream(
            $this->_streamName,
            $this,
            $context
        );

        return $this;
    }

    /**
     * Close the current stream.
     */
    final public function close()
    {
        $streamName = $this->getStreamName();

        if (null === $streamName) {
            return;
        }

        $name = \md5($streamName);

        if (!isset(self::$_register[$name])) {
            return;
        }

        Event::notify(
            'hoa://Event/Stream/'.$streamName.':close-before',
            $this,
            new EventBucket()
        );

        if (false === $this->_close()) {
            return;
        }

        unset(self::$_register[$name]);
        $this->_bucket[self::HANDLER] = null;
        Event::unregister(
            'hoa://Event/Stream/'.$streamName
        );
        Event::unregister(
            'hoa://Event/Stream/'.$streamName.':close-before'
        );

        return;
    }

    /**
     * Get the current stream name.
     */
    public function getStreamName()
    {
        if (empty($this->_bucket)) {
            return null;
        }

        return $this->_bucket[self::NAME];
    }

    /**
     * Get the current stream.
     */
    public function getStream()
    {
        if (empty($this->_bucket)) {
            return null;
        }

        return $this->_bucket[self::RESOURCE];
    }

    /**
     * Get the current stream context.
     */
    public function getStreamContext()
    {
        if (empty($this->_bucket)) {
            return null;
        }

        return $this->_bucket[self::CONTEXT];
    }

    /**
     * Get stream handler according to its name.
     */
    public static function getStreamHandler(string $streamName)
    {
        $name = \md5($streamName);

        if (!isset(self::$_register[$name])) {
            return null;
        }

        return self::$_register[$name][self::HANDLER];
    }

    /**
     * Set the current stream. Useful to manage a stack of streams (e.g. socket
     * and select). Notice that it could be unsafe to use this method without
     * taking time to think about it two minutes. Resource of type “Unknown” is
     * considered as valid.
     */
    public function _setStream($stream)
    {
        if (false === \is_resource($stream) &&
            ('resource' !== \gettype($stream) ||
             'Unknown' !== \get_resource_type($stream))) {
            throw new StreamException('Try to change the stream resource with an invalid one; '.'given %s.', 1, \gettype($stream));
        }

        $old = $this->_bucket[self::RESOURCE];
        $this->_bucket[self::RESOURCE] = $stream;

        return $old;
    }

    /**
     * Check if the stream is opened.
     */
    public function isOpened(): bool
    {
        return \is_resource($this->getStream());
    }

    /**
     * Set the timeout period.
     */
    public function setStreamTimeout(int $seconds, int $microseconds = 0): bool
    {
        return \stream_set_timeout($this->getStream(), $seconds, $microseconds);
    }

    /**
     * Whether the opening of the stream has been deferred.
     */
    protected function hasBeenDeferred()
    {
        return $this->_hasBeenDeferred;
    }

    /**
     * Check whether the connection has timed out or not.
     * This is basically a shortcut of `getStreamMetaData` + the `timed_out`
     * index, but the resulting code is more readable.
     */
    public function hasTimedOut(): bool
    {
        $metaData = $this->getStreamMetaData();

        return true === $metaData['timed_out'];
    }

    /**
     * Set blocking/non-blocking mode.
     */
    public function setStreamBlocking(bool $mode): bool
    {
        return \stream_set_blocking($this->getStream(), $mode);
    }

    /**
     * Set stream buffer.
     * Output using fwrite() (or similar function) is normally buffered at 8 Ko.
     * This means that if there are two processes wanting to write to the same
     * output stream, each is paused after 8 Ko of data to allow the other to
     * write.
     */
    public function setStreamBuffer(int $buffer): bool
    {
        // Zero means success.
        $out = 0 === \stream_set_write_buffer($this->getStream(), $buffer);

        if (true === $out) {
            $this->_bufferSize = $buffer;
        }

        return $out;
    }

    /**
     * Disable stream buffering.
     * Alias of $this->setBuffer(0).
     */
    public function disableStreamBuffer(): bool
    {
        return $this->setStreamBuffer(0);
    }

    /**
     * Get stream buffer size.
     */
    public function getStreamBufferSize(): int
    {
        return $this->_bufferSize;
    }

    /**
     * Get stream wrapper name.
     */
    public function getStreamWrapperName(): string
    {
        if (false === $pos = \strpos($this->getStreamName(), '://')) {
            return 'file';
        }

        return \substr($this->getStreamName(), 0, $pos);
    }

    /**
     * Get stream meta data.
     */
    public function getStreamMetaData(): array
    {
        return \stream_get_meta_data($this->getStream());
    }

    /**
     * Whether this stream is already opened by another handler.
     */
    public function isBorrowing(): bool
    {
        return $this->_borrowing;
    }

    /**
     * Notification callback.
     */
    public function _notify(
        int $ncode,
        int $severity,
        $message,
        $code,
        $transferred,
        $max
    ) {
        static $_map = [
            \STREAM_NOTIFY_AUTH_REQUIRED => 'authrequire',
            \STREAM_NOTIFY_AUTH_RESULT   => 'authresult',
            \STREAM_NOTIFY_COMPLETED     => 'complete',
            \STREAM_NOTIFY_CONNECT       => 'connect',
            \STREAM_NOTIFY_FAILURE       => 'failure',
            \STREAM_NOTIFY_MIME_TYPE_IS  => 'mimetype',
            \STREAM_NOTIFY_PROGRESS      => 'progress',
            \STREAM_NOTIFY_REDIRECTED    => 'redirect',
            \STREAM_NOTIFY_RESOLVE       => 'resolve',
            \STREAM_NOTIFY_FILE_SIZE_IS  => 'size',
        ];

        $this->getListener()->fire($_map[$ncode], new EventBucket([
            'code'        => $code,
            'severity'    => $severity,
            'message'     => $message,
            'transferred' => $transferred,
            'max'         => $max,
        ]));
    }

    /**
     * Call the $handler->close() method on each stream in the static stream
     * register.
     * This method does not check the return value of $handler->close(). Thus,
     * if a stream is persistent, the $handler->close() should do anything. It
     * is a very generic method.
     */
    final public static function _Hoa_Stream()
    {
        foreach (self::$_register as $entry) {
            $entry[self::HANDLER]->close();
        }

        return;
    }

    /**
     * Transform object to string.
     */
    public function __toString(): string
    {
        return $this->getStreamName();
    }

    /**
     * Close the stream when destructing.
     */
    public function __destruct()
    {
        if (false === $this->isOpened()) {
            return;
        }

        $this->close();

        return;
    }
}

/**
 * Class \Hoa\Stream\_Protocol.
 *
 * The `hoa://Library/Stream` node.
 *
 * @license    New BSD License
 */
class _Protocol extends ProtocolNode
{
    /**
     * Component's name.
     *
     * @var string
     */
    protected $_name = 'Stream';

    /**
     * ID of the component.
     *
     * @param string $id ID of the component
     *
     * @return mixed
     */
    public function reachId(string $id)
    {
        return Stream::getStreamHandler($id);
    }
}

/*
 * Shutdown method.
 */
\register_shutdown_function([Stream::class, '_Hoa_Stream']);

/**
 * Add the `hoa://Library/Stream` node. Should be use to reach/get an entry
 * in the stream register.
 */
$protocol = Protocol::getInstance();
$protocol['Library'][] = new _Protocol();
