<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Handler;

use SimpleXMLElement;
use Whoops\Exception\Formatter;

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
        $response = [
            'error' => Formatter::formatExceptionAsDataArray(
                $this->getInspector(),
                $this->addTraceToOutput(),
                $this->getRun()->getFrameFilters()
            ),
        ];

        echo self::toXml($response);

        return Handler::QUIT;
    }

    /**
     * @return string
     */
    public function contentType()
    {
        return 'application/xml';
    }

    /**
     * @param  SimpleXMLElement  $node Node to append data to, will be modified in place
     * @param  array|\Traversable $data
     * @return SimpleXMLElement  The modified node, for chaining
     */
    private static function addDataToNode(\SimpleXMLElement $node, $data)
    {
        assert(is_array($data) || $data instanceof Traversable);

        foreach ($data as $key => $value) {
            if (is_numeric($key)) {
                // Convert the key to a valid string
                $key = "unknownNode_". (string) $key;
            }

            // Delete any char not allowed in XML element names
            $key = preg_replace('/[^a-z0-9\-\_\.\:]/i', '', $key);

            if (is_array($value)) {
                $child = $node->addChild($key);
                self::addDataToNode($child, $value);
            } else {
                $value = str_replace('&', '&amp;', print_r($value, true));
                $node->addChild($key, $value);
            }
        }

        return $node;
    }

    /**
     * The main function for converting to an XML document.
     *
     * @param  array|\Traversable $data
     * @return string            XML
     */
    private static function toXml($data)
    {
        assert(is_array($data) || $data instanceof Traversable);

        $node = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><root />");

        return self::addDataToNode($node, $data)->asXML();
    }
}
