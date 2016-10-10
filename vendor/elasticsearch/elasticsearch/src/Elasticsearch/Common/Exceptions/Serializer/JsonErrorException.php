<?php

namespace Elasticsearch\Common\Exceptions\Serializer;

use Elasticsearch\Common\Exceptions\ElasticsearchException;

/**
 * Class JsonErrorException
 *
 * @category Elasticsearch
 * @package  Elasticsearch\Common\Exceptions\Curl
 * @author   Bez Hermoso <bezalelhermoso@gmail.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache2
 * @link     http://elasticsearch.org
 */
class JsonErrorException extends \Exception implements ElasticsearchException
{
    /**
     * @var mixed
     */
    private $input;

    /**
     * @var mixed
     */
    private $result;

    private static $messages = array(
        JSON_ERROR_DEPTH => 'The maximum stack depth has been exceeded',
        JSON_ERROR_STATE_MISMATCH => 'Invalid or malformed JSON',
        JSON_ERROR_CTRL_CHAR => 'Control character error, possibly incorrectly encoded',
        JSON_ERROR_SYNTAX => 'Syntax error',
        JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, possibly incorrectly encoded',

        // JSON_ERROR_* constant values that are available on PHP >= 5.5.0
        6 => 'One or more recursive references in the value to be encoded',
        7 => 'One or more NAN or INF values in the value to be encoded',
        8 => 'A value of a type that cannot be encoded was given',

    );

    public function __construct($code, $input, $result, $previous = null)
    {
        if (isset(self::$messages[$code]) !== true) {
            throw new \InvalidArgumentException(sprintf('%d is not a valid JSON error code.', $code));
        }

        parent::__construct(self::$messages[$code], $code, $previous);
        $this->input = $input;
        $this->result = $result;
    }

    /**
     * @return mixed
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }
}
