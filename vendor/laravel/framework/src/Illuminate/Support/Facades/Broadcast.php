<?php

namespace Illuminate\Support\Facades;

use Illuminate\Contracts\Broadcasting\Factory as BroadcastingFactoryContract;

/**
 * @method static void routes(array|null $attributes = null)
 * @method static void userRoutes(array|null $attributes = null)
 * @method static void channelRoutes(array|null $attributes = null)
 * @method static string|null socket(\Illuminate\Http\Request|null $request = null)
 * @method static \Illuminate\Broadcasting\AnonymousEvent on(\Illuminate\Broadcasting\Channel|array|string $channels)
 * @method static \Illuminate\Broadcasting\AnonymousEvent private(string $channel)
 * @method static \Illuminate\Broadcasting\AnonymousEvent presence(string $channel)
 * @method static \Illuminate\Broadcasting\PendingBroadcast event(mixed $event = null)
 * @method static void queue(mixed $event)
 * @method static mixed connection(string|null $driver = null)
 * @method static mixed driver(string|null $name = null)
 * @method static \Pusher\Pusher pusher(array $config)
 * @method static \Ably\AblyRest ably(array $config)
 * @method static string getDefaultDriver()
 * @method static void setDefaultDriver(string $name)
 * @method static void purge(string|null $name = null)
 * @method static \Illuminate\Broadcasting\BroadcastManager extend(string $driver, \Closure $callback)
 * @method static \Illuminate\Contracts\Foundation\Application getApplication()
 * @method static \Illuminate\Broadcasting\BroadcastManager setApplication(\Illuminate\Contracts\Foundation\Application $app)
 * @method static \Illuminate\Broadcasting\BroadcastManager forgetDrivers()
 * @method static mixed auth(\Illuminate\Http\Request $request)
 * @method static mixed validAuthenticationResponse(\Illuminate\Http\Request $request, mixed $result)
 * @method static void broadcast(array $channels, string $event, array $payload = [])
 * @method static array|null resolveAuthenticatedUser(\Illuminate\Http\Request $request)
 * @method static void resolveAuthenticatedUserUsing(\Closure $callback)
 * @method static \Illuminate\Broadcasting\Broadcasters\Broadcaster channel(\Illuminate\Contracts\Broadcasting\HasBroadcastChannel|string $channel, callable|string $callback, array $options = [])
 * @method static \Illuminate\Support\Collection getChannels()
 *
 * @see \Illuminate\Broadcasting\BroadcastManager
 * @see \Illuminate\Broadcasting\Broadcasters\Broadcaster
 */
class Broadcast extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return BroadcastingFactoryContract::class;
    }
}
