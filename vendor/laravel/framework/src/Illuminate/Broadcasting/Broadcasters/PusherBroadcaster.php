<?php

namespace Illuminate\Broadcasting\Broadcasters;

use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Pusher\ApiErrorException;
use Pusher\Pusher;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class PusherBroadcaster extends Broadcaster
{
    use UsePusherChannelConventions;

    /**
     * The Pusher SDK instance.
     *
     * @var \Pusher\Pusher
     */
    protected $pusher;

    /**
     * Indicates if JSONP callbacks are allowed on authorization.
     *
     * @var bool
     */
    protected $allowJsonp = false;

    /**
     * Create a new broadcaster instance.
     *
     * @param  \Pusher\Pusher  $pusher
     */
    public function __construct(Pusher $pusher, bool $allowJsonp = false)
    {
        $this->pusher = $pusher;
        $this->allowJsonp = $allowJsonp;
    }

    /**
     * Resolve the authenticated user payload for an incoming connection request.
     *
     * See: https://pusher.com/docs/channels/library_auth_reference/auth-signatures/#user-authentication
     * See: https://pusher.com/docs/channels/server_api/authenticating-users/#response
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|null
     */
    public function resolveAuthenticatedUser($request)
    {
        if (! $user = parent::resolveAuthenticatedUser($request)) {
            return;
        }

        if (method_exists($this->pusher, 'authenticateUser')) {
            return $this->pusher->authenticateUser($request->socket_id, $user);
        }

        $settings = $this->pusher->getSettings();
        $encodedUser = json_encode($user);
        $decodedString = "{$request->socket_id}::user::{$encodedUser}";

        $auth = $settings['auth_key'].':'.hash_hmac(
            'sha256', $decodedString, $settings['secret']
        );

        return [
            'auth' => $auth,
            'user_data' => $encodedUser,
        ];
    }

    /**
     * Authenticate the incoming request for a given channel.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     *
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function auth($request)
    {
        $channelName = $this->normalizeChannelName($request->channel_name);

        if (empty($request->channel_name) ||
            ($this->isGuardedChannel($request->channel_name) &&
            ! $this->retrieveUser($request, $channelName))) {
            throw new AccessDeniedHttpException;
        }

        return parent::verifyUserCanAccessChannel(
            $request, $channelName
        );
    }

    /**
     * Return the valid authentication response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $result
     * @return mixed
     */
    public function validAuthenticationResponse($request, $result)
    {
        if (str_starts_with($request->channel_name, 'private')) {
            return $this->decodePusherResponse(
                $request,
                method_exists($this->pusher, 'authorizeChannel')
                    ? $this->pusher->authorizeChannel($request->channel_name, $request->socket_id)
                    : $this->pusher->socket_auth($request->channel_name, $request->socket_id)
            );
        }

        $channelName = $this->normalizeChannelName($request->channel_name);

        $user = $this->retrieveUser($request, $channelName);

        $broadcastIdentifier = method_exists($user, 'getAuthIdentifierForBroadcasting')
            ? $user->getAuthIdentifierForBroadcasting()
            : $user->getAuthIdentifier();

        return $this->decodePusherResponse(
            $request,
            method_exists($this->pusher, 'authorizePresenceChannel')
                ? $this->pusher->authorizePresenceChannel($request->channel_name, $request->socket_id, $broadcastIdentifier, $result)
                : $this->pusher->presence_auth($request->channel_name, $request->socket_id, $broadcastIdentifier, $result)
        );
    }

    /**
     * Decode the given Pusher response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $response
     * @return array
     */
    protected function decodePusherResponse($request, $response)
    {
        if (! $request->input('callback', false) || ! $this->allowJsonp) {
            return json_decode($response, true);
        }

        return response()->json(json_decode($response, true))
            ->withCallback($request->callback);
    }

    /**
     * Broadcast the given event.
     *
     * @param  array  $channels
     * @param  string  $event
     * @param  array  $payload
     * @return void
     *
     * @throws \Illuminate\Broadcasting\BroadcastException
     */
    public function broadcast(array $channels, $event, array $payload = [])
    {
        $socket = Arr::pull($payload, 'socket');

        $parameters = $socket !== null ? ['socket_id' => $socket] : [];

        $channels = new Collection($this->formatChannels($channels));

        try {
            $channels->chunk(100)->each(function ($channels) use ($event, $payload, $parameters) {
                $this->pusher->trigger($channels->toArray(), $event, $payload, $parameters);
            });
        } catch (ApiErrorException $e) {
            throw new BroadcastException(
                sprintf('Pusher error: %s.', $e->getMessage())
            );
        }
    }

    /**
     * Get the Pusher SDK instance.
     *
     * @return \Pusher\Pusher
     */
    public function getPusher()
    {
        return $this->pusher;
    }

    /**
     * Set the Pusher SDK instance.
     *
     * @param  \Pusher\Pusher  $pusher
     * @return void
     */
    public function setPusher($pusher)
    {
        $this->pusher = $pusher;
    }
}
