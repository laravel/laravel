<?php

namespace GuzzleHttp\Handler;

use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\TransportSharing;
use Psr\Http\Message\RequestInterface;

/**
 * HTTP handler that uses cURL easy handles as a transport layer.
 *
 * When using the CurlHandler, custom curl options can be specified as an
 * associative array of curl option constants mapping to values in the
 * **curl** key of the "client" key of the request.
 *
 * @final
 */
class CurlHandler
{
    private const KNOWN_CONSTRUCTOR_OPTIONS = [
        'handle_factory' => true,
        'transport_sharing' => true,
    ];

    /**
     * @var CurlFactoryInterface
     */
    private $factory;

    /**
     * @var CurlShareHandleState|null
     */
    private $shareHandleState;

    /**
     * Accepts an associative array of options:
     *
     * - handle_factory: Optional curl factory used to create cURL handles.
     * - transport_sharing: Optional transport sharing mode.
     *
     * @param array{handle_factory?: ?CurlFactoryInterface, transport_sharing?: mixed} $options Array of options to use with the handler
     */
    public function __construct(array $options = [])
    {
        foreach ($options as $name => $_) {
            if (!isset(self::KNOWN_CONSTRUCTOR_OPTIONS[$name])) {
                \trigger_deprecation('guzzlehttp/guzzle', '7.14', \sprintf('The "%s" CurlHandler constructor option is unknown; guzzlehttp/guzzle 8.0 will reject unknown constructor options.', (string) $name));
            }
        }

        CurlShareHandleState::assertNoRequiredSharingCustomFactoryConflict($options, 'CurlHandler');
        $transportSharing = $options['transport_sharing'] ?? null;
        $sharingMode = CurlShareHandleState::normalizeMode($transportSharing, 'transport_sharing');

        if (\array_key_exists('handle_factory', $options) && $options['handle_factory'] !== null) {
            $this->shareHandleState = null;
            $this->factory = $options['handle_factory'];

            return;
        }

        $this->shareHandleState = $sharingMode !== TransportSharing::NONE
            ? CurlShareHandleState::fromOption($transportSharing)
            : null;

        $this->factory = $this->shareHandleState !== null
            ? new CurlFactory(3, $this->shareHandleState->mode, $this->shareHandleState)
            : new CurlFactory(3);
    }

    public function __invoke(RequestInterface $request, array $options): PromiseInterface
    {
        if (isset($options['delay'])) {
            \usleep($options['delay'] * 1000);
        }

        // A Multiplexing::NONE request option holds unconditionally here:
        // transport sharing never shares the connection cache on this
        // branch, and nothing else executes during the blocking curl_exec(),
        // so the transfer cannot share its connection with a concurrent
        // transfer.
        $easy = $this->factory->create($request, $options);
        \curl_exec($easy->handle);
        $easy->errno = \curl_errno($easy->handle);

        return CurlFactory::finish($this, $easy, $this->factory);
    }
}
