<?php namespace Laravel; defined('DS') or die('No direct script access.');

class Proxy {

    /**
     * Get proxy parameters from the operating system and configure a context.
     *
     * @return resource
     */
    public static function http()
    {
        $proxy = getenv('http_proxy');

        if (is_null($proxy) || $proxy == '') return null;

        $url = parse_url($proxy);

        $params = array(
            'request_fulluri' => true,
            'proxy' => "tcp://{$url['host']}:{$url['port']}"
        );

        if (isset($url['user']))
        {
            $auth = base64_encode($url['user'] . ':' . $url['pass']);
            $params['header'] = "Proxy-Authorization: Basic {$auth}";
        }

        return stream_context_create(array('http' => $params));
    }
}