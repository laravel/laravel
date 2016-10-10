<?php
namespace GuzzleHttp\Ring\Client;

use GuzzleHttp\Ring\Future\FutureArray;
use React\Promise\Deferred;

/**
 * Returns an asynchronous response using curl_multi_* functions.
 *
 * This handler supports future responses and the "delay" request client
 * option that can be used to delay before sending a request.
 *
 * When using the CurlMultiHandler, custom curl options can be specified as an
 * associative array of curl option constants mapping to values in the
 * **curl** key of the "client" key of the request.
 *
 * @property resource $_mh Internal use only. Lazy loaded multi-handle.
 */
class CurlMultiHandler
{
    /** @var callable */
    private $factory;
    private $selectTimeout;
    private $active;
    private $handles = [];
    private $delays = [];
    private $maxHandles;

    /**
     * This handler accepts the following options:
     *
     * - mh: An optional curl_multi resource
     * - handle_factory: An optional callable used to generate curl handle
     *   resources. the callable accepts a request hash and returns an array
     *   of the handle, headers file resource, and the body resource.
     * - select_timeout: Optional timeout (in seconds) to block before timing
     *   out while selecting curl handles. Defaults to 1 second.
     * - max_handles: Optional integer representing the maximum number of
     *   open requests. When this number is reached, the queued futures are
     *   flushed.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        if (isset($options['mh'])) {
            $this->_mh = $options['mh'];
        }
        $this->factory = isset($options['handle_factory'])
            ? $options['handle_factory'] : new CurlFactory();
        $this->selectTimeout = isset($options['select_timeout'])
            ? $options['select_timeout'] : 1;
        $this->maxHandles = isset($options['max_handles'])
            ? $options['max_handles'] : 100;
    }

    public function __get($name)
    {
        if ($name === '_mh') {
            return $this->_mh = curl_multi_init();
        }

        throw new \BadMethodCallException();
    }

    public function __destruct()
    {
        // Finish any open connections before terminating the script.
        if ($this->handles) {
            $this->execute();
        }

        if (isset($this->_mh)) {
            curl_multi_close($this->_mh);
            unset($this->_mh);
        }
    }

    public function __invoke(array $request)
    {
        $factory = $this->factory;
        $result = $factory($request);
        $entry = [
            'request'  => $request,
            'response' => [],
            'handle'   => $result[0],
            'headers'  => &$result[1],
            'body'     => $result[2],
            'deferred' => new Deferred(),
        ];

        $id = (int) $result[0];

        $future = new FutureArray(
            $entry['deferred']->promise(),
            [$this, 'execute'],
            function () use ($id) {
                return $this->cancel($id);
            }
        );

        $this->addRequest($entry);

        // Transfer outstanding requests if there are too many open handles.
        if (count($this->handles) >= $this->maxHandles) {
            $this->execute();
        }

        return $future;
    }

    /**
     * Runs until all outstanding connections have completed.
     */
    public function execute()
    {
        do {

            if ($this->active &&
                curl_multi_select($this->_mh, $this->selectTimeout) === -1
            ) {
                // Perform a usleep if a select returns -1.
                // See: https://bugs.php.net/bug.php?id=61141
                usleep(250);
            }

            // Add any delayed futures if needed.
            if ($this->delays) {
                $this->addDelays();
            }

            do {
                $mrc = curl_multi_exec($this->_mh, $this->active);
            } while ($mrc === CURLM_CALL_MULTI_PERFORM);

            $this->processMessages();

            // If there are delays but no transfers, then sleep for a bit.
            if (!$this->active && $this->delays) {
                usleep(500);
            }

        } while ($this->active || $this->handles);
    }

    private function addRequest(array &$entry)
    {
        $id = (int) $entry['handle'];
        $this->handles[$id] = $entry;

        // If the request is a delay, then add the reques to the curl multi
        // pool only after the specified delay.
        if (isset($entry['request']['client']['delay'])) {
            $this->delays[$id] = microtime(true) + ($entry['request']['client']['delay'] / 1000);
        } elseif (empty($entry['request']['future'])) {
            curl_multi_add_handle($this->_mh, $entry['handle']);
        } else {
            curl_multi_add_handle($this->_mh, $entry['handle']);
            // "lazy" futures are only sent once the pool has many requests.
            if ($entry['request']['future'] !== 'lazy') {
                do {
                    $mrc = curl_multi_exec($this->_mh, $this->active);
                } while ($mrc === CURLM_CALL_MULTI_PERFORM);
                $this->processMessages();
            }
        }
    }

    private function removeProcessed($id)
    {
        if (isset($this->handles[$id])) {
            curl_multi_remove_handle(
                $this->_mh,
                $this->handles[$id]['handle']
            );
            curl_close($this->handles[$id]['handle']);
            unset($this->handles[$id], $this->delays[$id]);
        }
    }

    /**
     * Cancels a handle from sending and removes references to it.
     *
     * @param int $id Handle ID to cancel and remove.
     *
     * @return bool True on success, false on failure.
     */
    private function cancel($id)
    {
        // Cannot cancel if it has been processed.
        if (!isset($this->handles[$id])) {
            return false;
        }

        $handle = $this->handles[$id]['handle'];
        unset($this->delays[$id], $this->handles[$id]);
        curl_multi_remove_handle($this->_mh, $handle);
        curl_close($handle);

        return true;
    }

    private function addDelays()
    {
        $currentTime = microtime(true);

        foreach ($this->delays as $id => $delay) {
            if ($currentTime >= $delay) {
                unset($this->delays[$id]);
                curl_multi_add_handle(
                    $this->_mh,
                    $this->handles[$id]['handle']
                );
            }
        }
    }

    private function processMessages()
    {
        while ($done = curl_multi_info_read($this->_mh)) {
            $id = (int) $done['handle'];

            if (!isset($this->handles[$id])) {
                // Probably was cancelled.
                continue;
            }

            $entry = $this->handles[$id];
            $entry['response']['transfer_stats'] = curl_getinfo($done['handle']);

            if ($done['result'] !== CURLM_OK) {
                $entry['response']['curl']['errno'] = $done['result'];
                if (function_exists('curl_strerror')) {
                    $entry['response']['curl']['error'] = curl_strerror($done['result']);
                }
            }

            $result = CurlFactory::createResponse(
                $this,
                $entry['request'],
                $entry['response'],
                $entry['headers'],
                $entry['body']
            );

            $this->removeProcessed($id);
            $entry['deferred']->resolve($result);
        }
    }
}
