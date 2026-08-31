<?php

namespace Illuminate\Broadcasting\Broadcasters;

use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Contracts\Redis\Factory as Redis;
use Illuminate\Redis\Connections\PhpRedisClusterConnection;
use Illuminate\Redis\Connections\PredisClusterConnection;
use Illuminate\Redis\Connections\PredisConnection;
use Illuminate\Support\Arr;
use Predis\Connection\Cluster\RedisCluster;
use Predis\Connection\ConnectionException;
use RedisException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class RedisBroadcaster extends Broadcaster
{
    use UsePusherChannelConventions;

    /**
     * The Redis instance.
     *
     * @var \Illuminate\Contracts\Redis\Factory
     */
    protected $redis;

    /**
     * The Redis connection to use for broadcasting.
     *
     * @var string|null
     */
    protected $connection = null;

    /**
     * The Redis key prefix.
     *
     * @var string
     */
    protected $prefix = '';

    /**
     * Create a new broadcaster instance.
     *
     * @param  \Illuminate\Contracts\Redis\Factory  $redis
     * @param  string|null  $connection
     * @param  string  $prefix
     */
    public function __construct(Redis $redis, $connection = null, $prefix = '')
    {
        $this->redis = $redis;
        $this->prefix = $prefix;
        $this->connection = $connection;
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
        $channelName = $this->normalizeChannelName(
            str_replace($this->prefix, '', $request->channel_name)
        );

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
        if (is_bool($result)) {
            return json_encode($result);
        }

        $channelName = $this->normalizeChannelName($request->channel_name);

        $user = $this->retrieveUser($request, $channelName);

        $broadcastIdentifier = method_exists($user, 'getAuthIdentifierForBroadcasting')
            ? $user->getAuthIdentifierForBroadcasting()
            : $user->getAuthIdentifier();

        return json_encode(['channel_data' => [
            'user_id' => $broadcastIdentifier,
            'user_info' => $result,
        ]]);
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
        if (empty($channels)) {
            return;
        }

        $connection = $this->redis->connection($this->connection);

        $payload = json_encode([
            'event' => $event,
            'data' => $payload,
            'socket' => Arr::pull($payload, 'socket'),
        ]);

        try {
            if ($connection instanceof PhpRedisClusterConnection) {
                foreach ($channels as $channel) {
                    $connection->publish($channel, $payload);
                }
            } elseif ($connection instanceof PredisClusterConnection &&
                $connection->client()->getConnection() instanceof RedisCluster) {
                $randomClusterNodeConnection = new PredisConnection(
                    $connection->client()->getClientBy('slot', mt_rand(0, 16383))
                );

                if ($events = $connection->getEventDispatcher()) {
                    $randomClusterNodeConnection->setEventDispatcher($events);
                }

                $randomClusterNodeConnection->eval(
                    $this->broadcastMultipleChannelsScript(),
                    0, $payload, ...$this->formatChannels($channels)
                );
            } else {
                $connection->eval(
                    $this->broadcastMultipleChannelsScript(),
                    0, $payload, ...$this->formatChannels($channels)
                );
            }
        } catch (ConnectionException|RedisException $e) {
            throw new BroadcastException(
                sprintf('Redis error: %s.', $e->getMessage())
            );
        }
    }

    /**
     * Get the Lua script for broadcasting to multiple channels.
     *
     * ARGV[1] - The payload
     * ARGV[2...] - The channels
     *
     * @return string
     */
    protected function broadcastMultipleChannelsScript()
    {
        return <<<'LUA'
for i = 2, #ARGV do
  redis.call('publish', ARGV[i], ARGV[1])
end
LUA;
    }

    /**
     * Format the channel array into an array of strings.
     *
     * @param  array  $channels
     * @return array
     */
    protected function formatChannels(array $channels)
    {
        return array_map(function ($channel) {
            return $this->prefix.$channel;
        }, parent::formatChannels($channels));
    }
}
