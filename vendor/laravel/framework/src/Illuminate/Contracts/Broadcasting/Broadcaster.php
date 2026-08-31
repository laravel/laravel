<?php

namespace Illuminate\Contracts\Broadcasting;

interface Broadcaster
{
    /**
     * Authenticate the incoming request for a given channel.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function auth($request);

    /**
     * Return the valid authentication response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $result
     * @return mixed
     */
    public function validAuthenticationResponse($request, $result);

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
    public function broadcast(array $channels, $event, array $payload = []);
}
