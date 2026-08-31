<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Handler;

use Whoops\Exception\Formatter;

/**
 * Catches an exception and converts it to a JSON
 * response. Additionally can also return exception
 * frames for consumption by an API.
 */
class JsonResponseHandler extends Handler
{
    /**
     * @var bool
     */
    private $returnFrames = false;

    /**
     * @var bool
     */
    private $jsonApi = false;

    /**
     * Returns errors[[]] instead of error[] to be in compliance with the json:api spec
     * @param bool $jsonApi Default is false
     * @return static
     */
    public function setJsonApi($jsonApi = false)
    {
        $this->jsonApi = (bool) $jsonApi;
        return $this;
    }

    /**
     * @param  bool|null  $returnFrames
     * @return bool|static
     */
    public function addTraceToOutput($returnFrames = null)
    {
        if (func_num_args() == 0) {
            return $this->returnFrames;
        }

        $this->returnFrames = (bool) $returnFrames;
        return $this;
    }

    /**
     * @return int
     */
    public function handle()
    {
        if ($this->jsonApi === true) {
            $response = [
                'errors' => [
                    Formatter::formatExceptionAsDataArray(
                        $this->getInspector(),
                        $this->addTraceToOutput(),
                        $this->getRun()->getFrameFilters()
                    ),
                ]
            ];
        } else {
            $response = [
                'error' => Formatter::formatExceptionAsDataArray(
                    $this->getInspector(),
                    $this->addTraceToOutput(),
                    $this->getRun()->getFrameFilters()
                ),
            ];
        }

        echo json_encode($response, defined('JSON_PARTIAL_OUTPUT_ON_ERROR') ? JSON_PARTIAL_OUTPUT_ON_ERROR : 0);

        return Handler::QUIT;
    }

    /**
     * @return string
     */
    public function contentType()
    {
        return 'application/json';
    }
}
