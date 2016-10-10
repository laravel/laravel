<?php

namespace Elasticsearch\Namespaces;

use Elasticsearch\Common\Exceptions\Missing404Exception;
use Elasticsearch\Common\Exceptions\RoutingMissingException;
use Elasticsearch\Endpoints\AbstractEndpoint;
use GuzzleHttp\Ring\Future\FutureArrayInterface;

/**
 * Trait AbstractNamespace
 *
 * @category Elasticsearch
 * @package  Elasticsearch\Namespaces
 * @author   Zachary Tong <zachary.tong@elasticsearch.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache2
 * @link     http://elasticsearch.org
 */
trait BooleanRequestWrapper
{
    /**
     * Perform Request
     *
     * @param  AbstractEndpoint $endpoint The Endpoint to perform this request against
     *
     * @throws Missing404Exception
     * @throws RoutingMissingException
     */
    public static function performRequest(AbstractEndpoint $endpoint)
    {
        try {
            $response = $endpoint->performRequest();
            $response = $endpoint->resultOrFuture($response);
            if (!($response instanceof FutureArrayInterface)) {
                if ($response['status'] === 200) {
                    return true;
                } else {
                    return false;
                }
            } else {
                // async mode, can't easily resolve this...punt to user
                return $response;
            }
        } catch (Missing404Exception $exception) {
            return false;
        } catch (RoutingMissingException $exception) {
            return false;
        }
    }
}
