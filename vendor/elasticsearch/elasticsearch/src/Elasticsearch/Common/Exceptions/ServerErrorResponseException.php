<?php

namespace Elasticsearch\Common\Exceptions;

/**
 * ServerErrorResponseException
 *
 * @category Elasticsearch
 * @package  Elasticsearch\Common\Exceptions
 * @author   Zachary Tong <zachary.tong@elasticsearch.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache2
 * @link     http://elasticsearch.org
 */
class ServerErrorResponseException extends TransportException implements ElasticsearchException
{
}
