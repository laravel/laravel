<?php

namespace GuzzleHttp;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Formats log messages using variable substitutions for requests, responses,
 * and other transactional data.
 *
 * The following variable substitutions are supported:
 *
 * - {request}:        Full HTTP request message
 * - {response}:       Full HTTP response message
 * - {ts}:             ISO 8601 date in GMT
 * - {date_iso_8601}   ISO 8601 date in GMT
 * - {date_common_log} Apache common log date using the configured timezone.
 * - {host}:           Host of the request
 * - {method}:         Method of the request
 * - {uri}:            URI of the request
 * - {version}:        Protocol version
 * - {target}:         Request target of the request (path + query + fragment)
 * - {hostname}:       Hostname of the machine that sent the request
 * - {code}:           Status code of the response (if available)
 * - {phrase}:         Reason phrase of the response  (if available)
 * - {error}:          Any error messages (if available)
 * - {req_header_*}:   Replace `*` with the lowercased name of a request header to add to the message
 * - {res_header_*}:   Replace `*` with the lowercased name of a response header to add to the message
 * - {req_headers}:    Request headers
 * - {res_headers}:    Response headers
 * - {req_body}:       Request body
 * - {res_body}:       Response body
 *
 * @final
 */
class MessageFormatter implements MessageFormatterInterface
{
    /**
     * Apache Common Log Format.
     *
     * @see https://httpd.apache.org/docs/2.4/logs.html#common
     *
     * @var string
     */
    public const CLF = '{hostname} {req_header_User-Agent} - [{date_common_log}] "{method} {target} HTTP/{version}" {code} {res_header_Content-Length}';
    public const DEBUG = ">>>>>>>>\n{request}\n<<<<<<<<\n{response}\n--------\n{error}";
    public const SHORT = '[{ts}] "{method} {target} HTTP/{version}" {code}';

    /**
     * @var string Template used to format log messages
     */
    private $template;

    /**
     * @param string $template Log message template
     */
    public function __construct(?string $template = self::CLF)
    {
        $this->template = $template ?: self::CLF;
    }

    /**
     * Returns a formatted message string.
     *
     * @param RequestInterface       $request  Request that was sent
     * @param ResponseInterface|null $response Response that was received
     * @param \Throwable|null        $error    Exception that was received
     */
    public function format(RequestInterface $request, ?ResponseInterface $response = null, ?\Throwable $error = null): string
    {
        $cache = [];

        $result = \preg_replace_callback(
            '/{\s*([A-Za-z_\-\.0-9]+)\s*}/',
            function (array $matches) use ($request, $response, $error, &$cache) {
                if (isset($cache[$matches[1]])) {
                    return $cache[$matches[1]];
                }

                $result = '';
                switch ($matches[1]) {
                    case 'request':
                        $result = Psr7\Message::toString($request);
                        break;
                    case 'response':
                        $result = $response ? Psr7\Message::toString($response) : '';
                        break;
                    case 'req_headers':
                        $result = \trim($request->getMethod()
                                .' '.$request->getRequestTarget(), " \n\r\t\0\x0B")
                            .' HTTP/'.$request->getProtocolVersion()."\r\n"
                            .$this->headers($request);
                        break;
                    case 'res_headers':
                        $result = $response ?
                            \sprintf(
                                'HTTP/%s %d %s',
                                $response->getProtocolVersion(),
                                $response->getStatusCode(),
                                $response->getReasonPhrase()
                            )."\r\n".$this->headers($response)
                            : 'NULL';
                        break;
                    case 'req_body':
                        $result = $request->getBody()->__toString();
                        break;
                    case 'res_body':
                        if (!$response instanceof ResponseInterface) {
                            $result = 'NULL';
                            break;
                        }

                        $body = $response->getBody();

                        if (!$body->isSeekable()) {
                            $result = 'RESPONSE_NOT_LOGGEABLE';
                            break;
                        }

                        $result = $response->getBody()->__toString();
                        break;
                    case 'ts':
                    case 'date_iso_8601':
                        $result = \gmdate('c');
                        break;
                    case 'date_common_log':
                        $result = \date('d/M/Y:H:i:s O');
                        break;
                    case 'method':
                        $result = $request->getMethod();
                        break;
                    case 'version':
                        $result = $request->getProtocolVersion();
                        break;
                    case 'uri':
                    case 'url':
                        $result = $request->getUri()->__toString();
                        break;
                    case 'target':
                        $result = $request->getRequestTarget();
                        break;
                    case 'req_version':
                        $result = $request->getProtocolVersion();
                        break;
                    case 'res_version':
                        $result = $response
                            ? $response->getProtocolVersion()
                            : 'NULL';
                        break;
                    case 'host':
                        $result = $request->getHeaderLine('Host');
                        break;
                    case 'hostname':
                        $result = \gethostname();
                        break;
                    case 'code':
                        $result = $response ? $response->getStatusCode() : 'NULL';
                        break;
                    case 'phrase':
                        $result = $response ? $response->getReasonPhrase() : 'NULL';
                        break;
                    case 'error':
                        $result = $error ? $error->getMessage() : 'NULL';
                        break;
                    default:
                        // handle prefixed dynamic headers
                        if (\strpos($matches[1], 'req_header_') === 0) {
                            $result = $request->getHeaderLine(\substr($matches[1], 11));
                        } elseif (\strpos($matches[1], 'res_header_') === 0) {
                            $result = $response
                                ? $response->getHeaderLine(\substr($matches[1], 11))
                                : 'NULL';
                        }
                }

                $cache[$matches[1]] = $result;

                return $result;
            },
            $this->template
        );

        if ($result === null) {
            throw new \RuntimeException('Unable to format message: '.\preg_last_error_msg());
        }

        return $result;
    }

    /**
     * Get headers from message as string
     */
    private function headers(MessageInterface $message): string
    {
        $result = '';
        foreach ($message->getHeaders() as $name => $values) {
            $result .= $name.': '.\implode(', ', $values)."\r\n";
        }

        return \trim($result, " \n\r\t\0\x0B");
    }
}
