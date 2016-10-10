<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Handler;

use SimpleXMLElement;
use Whoops\Exception\Frame;
use Whoops\Handler\Handler;

/**
 * Catches an exception and converts it to an XML
 * response. Additionally can also return exception
 * frames for consumption by an API.
 */
class XmlResponseHandler extends Handler
{
    /**
     * @var bool
     */
    private $returnFrames = false;

    /**
     * @param  bool|null $returnFrames
     * @return null|bool
     */
    public function addTraceToOutput($returnFrames = null)
    {
        if(func_num_args() == 0) {
            return $this->returnFrames;
        }

        $this->returnFrames = (bool) $returnFrames;
    }

    /**
     * @return int
     */
    public function handle()
    {
        $exception = $this->getException();

        $response = array(
            'error' => array(
                'type'    => get_class($exception),
                'message' => $exception->getMessage(),
                'file'    => $exception->getFile(),
                'line'    => $exception->getLine()
            )
        );

        if($this->addTraceToOutput()) {
            $inspector = $this->getInspector();
            $frames    = $inspector->getFrames();
            $frameData = array();

            foreach($frames as $frame) {
                /** @var Frame $frame */
                $frameData[] = array(
                    'file'     => $frame->getFile(),
                    'line'     => $frame->getLine(),
                    'function' => $frame->getFunction(),
                    'class'    => $frame->getClass(),
                    'args'     => $frame->getArgs()
                );
            }

            $response['error']['trace'] = array_flip($frameData);
        }

        echo $this->toXml($response);

        return Handler::QUIT;
    }

    /**
     * @param SimpleXMLElement $node Node to append data to, will be modified in place
     * @param array|Traversable $data
     * @return SimpleXMLElement The modified node, for chaining
     */
    private static function addDataToNode(\SimpleXMLElement $node, $data)
    {
        assert('is_array($data) || $node instanceof Traversable');

        foreach($data as $key => $value)
        {
            if (is_numeric($key))
            {
                // Convert the key to a valid string
                $key = "unknownNode_". (string) $key;
            }

            // Delete any char not allowed in XML element names
            $key = preg_replace('/[^a-z0-9\-\_\.\:]/i', '', $key);

            if (is_array($value))
            {
                $child = $node->addChild($key);
                self::addDataToNode($child, $value);
            }
            else
            {
                $value = str_replace('&', '&amp;', print_r($value, true));
                $node->addChild($key, $value);
            }
        }

        return $node;
    }

    /**
     * The main function for converting to an XML document.
     *
     * @param array|Traversable $data
     * @return string XML
     */
    private static function toXml($data)
    {
        assert('is_array($data) || $node instanceof Traversable');

        // turn off compatibility mode as simple xml throws a wobbly if you don't.
        $compatibilityMode = ini_get('zend.ze1_compatibility_mode');
        if ($compatibilityMode) {
            ini_set('zend.ze1_compatibility_mode', 0);
        }

        $node = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><root />");
        $xml = self::addDataToNode($node, $data)->asXML();

        if ($compatibilityMode) {
            ini_set('zend.ze1_compatibility_mode', $compatibilityMode);
        }
        return $xml;
    }
}
