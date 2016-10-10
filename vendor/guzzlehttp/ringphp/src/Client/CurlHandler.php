<?php
namespace GuzzleHttp\Ring\Client;

use GuzzleHttp\Ring\Future\CompletedFutureArray;
use GuzzleHttp\Ring\Core;

/**
 * HTTP handler that uses cURL easy handles as a transport layer.
 *
 * Requires PHP 5.5+
 *
 * When using the CurlHandler, custom curl options can be specified as an
 * associative array of curl option constants mapping to values in the
 * **curl** key of the "client" key of the request.
 */
class CurlHandler
{
    /** @var callable */
    private $factory;

    /** @var array Array of curl easy handles */
    private $handles = [];

    /** @var array Array of owned curl easy handles */
    private $ownedHandles = [];

    /** @var int Total number of idle handles to keep in cache */
    private $maxHandles;

    /**
     * Accepts an associative array of options:
     *
     * - factory: Optional callable factory used to create cURL handles.
     *   The callable is passed a request hash when invoked, and returns an
     *   array of the curl handle, headers resource, and body resource.
     * - max_handles: Maximum number of idle handles (defaults to 5).
     *
     * @param array $options Array of options to use with the handler
     */
    public function __construct(array $options = [])
    {
        $this->handles = $this->ownedHandles = [];
        $this->factory = isset($options['handle_factory'])
            ? $options['handle_factory']
            : new CurlFactory();
        $this->maxHandles = isset($options['max_handles'])
            ? $options['max_handles']
            : 5;
    }

    public function __destruct()
    {
        foreach ($this->handles as $handle) {
            if (is_resource($handle)) {
                curl_close($handle);
            }
        }
    }

    /**
     * @param array $request
     *
     * @return CompletedFutureArray
     */
    public function __invoke(array $request)
    {
        return new CompletedFutureArray(
            $this->_invokeAsArray($request)
        );
    }

    /**
     * @internal
     *
     * @param array $request
     *
     * @return array
     */
    public function _invokeAsArray(array $request)
    {
        $factory = $this->factory;

        // Ensure headers are by reference. They're updated elsewhere.
        $result = $factory($request, $this->checkoutEasyHandle());
        $h = $result[0];
        $hd =& $result[1];
        $bd = $result[2];
        Core::doSleep($request);
        curl_exec($h);
        $response = ['transfer_stats' => curl_getinfo($h)];
        $response['curl']['error'] = curl_error($h);
        $response['curl']['errno'] = curl_errno($h);
        $response['transfer_stats'] = array_merge($response['transfer_stats'], $response['curl']);
        $this->releaseEasyHandle($h);

        return CurlFactory::createResponse([$this, '_invokeAsArray'], $request, $response, $hd, $bd);
    }

    private function checkoutEasyHandle()
    {
        // Find an unused handle in the cache
        if (false !== ($key = array_search(false, $this->ownedHandles, true))) {
            $this->ownedHandles[$key] = true;
            return $this->handles[$key];
        }

        // Add a new handle
        $handle = curl_init();
        $id = (int) $handle;
        $this->handles[$id] = $handle;
        $this->ownedHandles[$id] = true;

        return $handle;
    }

    private function releaseEasyHandle($handle)
    {
        $id = (int) $handle;
        if (count($this->ownedHandles) > $this->maxHandles) {
            curl_close($this->handles[$id]);
            unset($this->handles[$id], $this->ownedHandles[$id]);
        } else {
            // curl_reset doesn't clear these out for some reason
            static $unsetValues = [
                CURLOPT_HEADERFUNCTION   => null,
                CURLOPT_WRITEFUNCTION    => null,
                CURLOPT_READFUNCTION     => null,
                CURLOPT_PROGRESSFUNCTION => null,
            ];
            curl_setopt_array($handle, $unsetValues);
            curl_reset($handle);
            $this->ownedHandles[$id] = false;
        }
    }
}
