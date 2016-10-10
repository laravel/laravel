=========
Changelog
=========

3.0.0 (2014-10-12)
------------------

* Now supports creating streams from functions and iterators.
* Supports creating buffered streams and asynchronous streams.
* Removed ``functions.php``. Use the corresponding functions provided by
  ``GuzzleHttp\Streams\Utils`` instead.
* Moved ``GuzzleHttp\Stream\MetadataStreamInterface::getMetadata`` to
  ``GuzzleHttp\Stream\StreamInterface``. MetadataStreamInterface is no longer
  used and is marked as deprecated.
* Added ``attach()`` to ``GuzzleHttp\Stream\StreamInterface`` for PSR-7
  compatibility.
* Removed ``flush()`` from StreamInterface.
* Removed the ``$maxLength`` parameter from
  ``GuzzleHttp\Stream\StreamInterface::getContents()``. This function now
  returns the entire remainder of the stream. If you want to limit the maximum
  amount of data read from the stream, use the
  ``GuzzleHttp\Stream\Utils::copyToString()`` function.
* Streams that return an empty string, ``''``, are no longer considered a
  failure. You MUST return ``false`` to mark the read as a failure, and ensure
  that any decorators you create properly return ``true`` in response to the
  ``eof()`` method when the stream is consumed.
* ``GuzzleHttp\Stream\Stream::__construct``,
  ``GuzzleHttp\Stream\Stream::factory``, and
  ``GuzzleHttp\Stream\Utils::create`` no longer accept a size in the second
  argument. They now accept an associative array of options, including the
  "size" key and "metadata" key which can be used to provide custom metadata.
* Added ``GuzzleHttp\Stream\BufferStream`` to add support for buffering data,
  and when read, shifting data off of the buffer.
* Added ``GuzzleHttp\Stream\NullBuffer`` which can be used as a buffer that
  does not actually store any data.
* Added ``GuzzleHttp\Stream\AsyncStream`` to provide support for non-blocking
  streams that can be filled by a remote source (e.g., an event-loop). If a
  ``drain`` option is provided, the stream can also act as if it is a blocking
  stream.

2.1.0 (2014-08-17)
------------------

* Added an InflateStream to inflate gzipped or deflated content.
* Added ``flush`` to stream wrapper.
* Added the ability to easily register the GuzzleStreamWrapper if needed.

2.0.0 (2014-08-16)
------------------

* Deprecated functions.php and moved all of those methods to
  ``GuzzleHttp\Streams\Utils``. Use ``GuzzleHttp\Stream\Stream::factory()``
  instead of ``GuzzleHttp\Stream\create()`` to create new streams.
* Added ``flush()`` to ``StreamInterface``. This method is used to flush any
  underlying stream write buffers.
* Added ``FnStream`` to easily decorate stream behavior with callables.
* ``Utils::hash`` now throws an exception when the stream cannot seek to 0.

1.5.1 (2014-09-10)
------------------

* Stream metadata is grabbed from the underlying stream each time
  ``getMetadata`` is called rather than returning a value from a cache.
* Properly closing all underlying streams when AppendStream is closed.
* Seek functions no longer throw exceptions.
* LazyOpenStream now correctly returns the underlying stream resource when
  detached.

1.5.0 (2014-08-07)
------------------

* Added ``Stream\safe_open`` to open stream resources and throw exceptions
  instead of raising errors.

1.4.0 (2014-07-19)
------------------

* Added a LazyOpenStream

1.3.0 (2014-07-15)
------------------

* Added an AppendStream to stream over multiple stream one after the other.

1.2.0 (2014-07-15)
------------------

* Updated the ``detach()`` method to return the underlying stream resource or
  ``null`` if it does not wrap a resource.
* Multiple fixes for how streams behave when the underlying resource is
  detached
* Do not clear statcache when a stream does not have a 'uri'
* Added a fix to LimitStream
* Added a condition to ensure that functions.php can be required multiple times
