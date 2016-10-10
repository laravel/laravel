<?php
namespace GuzzleHttp\Ring\Future;

/**
 * Represents a future value that responds to wait() to retrieve the promised
 * value, but can also return promises that are delivered the value when it is
 * available.
 */
class FutureValue implements FutureInterface
{
    use BaseFutureTrait;
}
