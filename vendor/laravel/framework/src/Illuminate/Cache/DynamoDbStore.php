<?php

namespace Illuminate\Cache;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Exception\DynamoDbException;
use Illuminate\Contracts\Cache\LockProvider;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\InteractsWithTime;
use Illuminate\Support\Str;
use RuntimeException;

class DynamoDbStore implements LockProvider, Store
{
    use InteractsWithTime;

    /**
     * A string that should be prepended to keys.
     *
     * @var string
     */
    protected $prefix;

    /**
     * The classes that should be allowed during unserialization.
     *
     * @var array|bool|null
     */
    protected $serializableClasses;

    /**
     * Create a new store instance.
     *
     * @param  \Aws\DynamoDb\DynamoDbClient  $dynamo  The DynamoDB client instance.
     * @param  string  $table  The table name.
     * @param  string  $keyAttribute  The name of the attribute that should hold the key.
     * @param  string  $valueAttribute  The name of the attribute that should hold the value.
     * @param  string  $expirationAttribute  The name of the attribute that should hold the expiration timestamp.
     * @param  string  $prefix
     * @param  array|bool|null  $serializableClasses
     */
    public function __construct(
        protected DynamoDbClient $dynamo,
        protected $table,
        protected $keyAttribute = 'key',
        protected $valueAttribute = 'value',
        protected $expirationAttribute = 'expires_at',
        $prefix = '',
        $serializableClasses = null,
    ) {
        $this->setPrefix($prefix);
        $this->serializableClasses = $serializableClasses;
    }

    /**
     * Retrieve an item from the cache by key.
     *
     * @param  string  $key
     * @return mixed
     */
    public function get($key)
    {
        $response = $this->dynamo->getItem([
            'TableName' => $this->table,
            'ConsistentRead' => false,
            'Key' => [
                $this->keyAttribute => [
                    'S' => $this->prefix.$key,
                ],
            ],
        ]);

        if (! isset($response['Item'])) {
            return;
        }

        if ($this->isExpired($response['Item'])) {
            return;
        }

        if (isset($response['Item'][$this->valueAttribute])) {
            return $this->unserialize(
                $response['Item'][$this->valueAttribute]['S'] ??
                $response['Item'][$this->valueAttribute]['N'] ??
                null
            );
        }
    }

    /**
     * Retrieve multiple items from the cache by key.
     *
     * Items not found in the cache will have a null value.
     *
     * @param  array  $keys
     * @return array
     */
    public function many(array $keys)
    {
        if (count($keys) === 0) {
            return [];
        }

        $prefixedKeys = array_map(function ($key) {
            return $this->prefix.$key;
        }, $keys);

        $response = $this->dynamo->batchGetItem([
            'RequestItems' => [
                $this->table => [
                    'ConsistentRead' => false,
                    'Keys' => (new Collection($prefixedKeys))->map(fn ($key) => [
                        $this->keyAttribute => [
                            'S' => $key,
                        ],
                    ])->all(),
                ],
            ],
        ]);

        $now = Carbon::now();

        return array_merge(
            Arr::mapWithKeys($keys, fn ($key) => [$key => null]),
            (new Collection($response['Responses'][$this->table]))->mapWithKeys(function ($response) use ($now) {
                if ($this->isExpired($response, $now)) {
                    $value = null;
                } else {
                    $value = $this->unserialize(
                        $response[$this->valueAttribute]['S'] ??
                        $response[$this->valueAttribute]['N'] ??
                        null
                    );
                }

                return [Str::replaceFirst($this->prefix, '', $response[$this->keyAttribute]['S']) => $value];
            })->all());
    }

    /**
     * Determine if the given item is expired.
     *
     * @param  array  $item
     * @param  \DateTimeInterface|null  $expiration
     * @return bool
     */
    protected function isExpired(array $item, $expiration = null)
    {
        $expiration = $expiration ?: Carbon::now();

        return isset($item[$this->expirationAttribute]) &&
               $expiration->getTimestamp() >= $item[$this->expirationAttribute]['N'];
    }

    /**
     * Store an item in the cache for a given number of seconds.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  int  $seconds
     * @return bool
     */
    public function put($key, $value, $seconds)
    {
        $this->dynamo->putItem([
            'TableName' => $this->table,
            'Item' => [
                $this->keyAttribute => [
                    'S' => $this->prefix.$key,
                ],
                $this->valueAttribute => [
                    $this->type($value) => $this->serialize($value),
                ],
                $this->expirationAttribute => [
                    'N' => (string) $this->toTimestamp($seconds),
                ],
            ],
        ]);

        return true;
    }

    /**
     * Store multiple items in the cache for a given number of seconds.
     *
     * @param  array  $values
     * @param  int  $seconds
     * @return bool
     */
    public function putMany(array $values, $seconds)
    {
        if (count($values) === 0) {
            return true;
        }

        $expiration = $this->toTimestamp($seconds);

        $this->dynamo->batchWriteItem([
            'RequestItems' => [
                $this->table => (new Collection($values))->map(function ($value, $key) use ($expiration) {
                    return [
                        'PutRequest' => [
                            'Item' => [
                                $this->keyAttribute => [
                                    'S' => $this->prefix.$key,
                                ],
                                $this->valueAttribute => [
                                    $this->type($value) => $this->serialize($value),
                                ],
                                $this->expirationAttribute => [
                                    'N' => (string) $expiration,
                                ],
                            ],
                        ],
                    ];
                })->values()->all(),
            ],
        ]);

        return true;
    }

    /**
     * Store an item in the cache if the key doesn't exist.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  int  $seconds
     * @return bool
     */
    public function add($key, $value, $seconds)
    {
        try {
            $this->dynamo->putItem([
                'TableName' => $this->table,
                'Item' => [
                    $this->keyAttribute => [
                        'S' => $this->prefix.$key,
                    ],
                    $this->valueAttribute => [
                        $this->type($value) => $this->serialize($value),
                    ],
                    $this->expirationAttribute => [
                        'N' => (string) $this->toTimestamp($seconds),
                    ],
                ],
                'ConditionExpression' => 'attribute_not_exists(#key) OR #expires_at < :now',
                'ExpressionAttributeNames' => [
                    '#key' => $this->keyAttribute,
                    '#expires_at' => $this->expirationAttribute,
                ],
                'ExpressionAttributeValues' => [
                    ':now' => [
                        'N' => (string) $this->currentTime(),
                    ],
                ],
            ]);

            return true;
        } catch (DynamoDbException $e) {
            if (str_contains($e->getMessage(), 'ConditionalCheckFailed')) {
                return false;
            }

            throw $e;
        }
    }

    /**
     * Increment the value of an item in the cache.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return int|false
     *
     * @throws \Aws\DynamoDb\Exception\DynamoDbException
     */
    public function increment($key, $value = 1)
    {
        try {
            $response = $this->dynamo->updateItem([
                'TableName' => $this->table,
                'Key' => [
                    $this->keyAttribute => [
                        'S' => $this->prefix.$key,
                    ],
                ],
                'ConditionExpression' => 'attribute_exists(#key) AND #expires_at > :now',
                'UpdateExpression' => 'SET #value = #value + :amount',
                'ExpressionAttributeNames' => [
                    '#key' => $this->keyAttribute,
                    '#value' => $this->valueAttribute,
                    '#expires_at' => $this->expirationAttribute,
                ],
                'ExpressionAttributeValues' => [
                    ':now' => [
                        'N' => (string) $this->currentTime(),
                    ],
                    ':amount' => [
                        'N' => (string) $value,
                    ],
                ],
                'ReturnValues' => 'UPDATED_NEW',
            ]);

            return (int) $response['Attributes'][$this->valueAttribute]['N'];
        } catch (DynamoDbException $e) {
            if (str_contains($e->getMessage(), 'ConditionalCheckFailed')) {
                return false;
            }

            throw $e;
        }
    }

    /**
     * Decrement the value of an item in the cache.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return int|false
     *
     * @throws \Aws\DynamoDb\Exception\DynamoDbException
     */
    public function decrement($key, $value = 1)
    {
        try {
            $response = $this->dynamo->updateItem([
                'TableName' => $this->table,
                'Key' => [
                    $this->keyAttribute => [
                        'S' => $this->prefix.$key,
                    ],
                ],
                'ConditionExpression' => 'attribute_exists(#key) AND #expires_at > :now',
                'UpdateExpression' => 'SET #value = #value - :amount',
                'ExpressionAttributeNames' => [
                    '#key' => $this->keyAttribute,
                    '#value' => $this->valueAttribute,
                    '#expires_at' => $this->expirationAttribute,
                ],
                'ExpressionAttributeValues' => [
                    ':now' => [
                        'N' => (string) $this->currentTime(),
                    ],
                    ':amount' => [
                        'N' => (string) $value,
                    ],
                ],
                'ReturnValues' => 'UPDATED_NEW',
            ]);

            return (int) $response['Attributes'][$this->valueAttribute]['N'];
        } catch (DynamoDbException $e) {
            if (str_contains($e->getMessage(), 'ConditionalCheckFailed')) {
                return false;
            }

            throw $e;
        }
    }

    /**
     * Store an item in the cache indefinitely.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return bool
     */
    public function forever($key, $value)
    {
        return $this->put($key, $value, Carbon::now()->addYears(5)->getTimestamp());
    }

    /**
     * Get a lock instance.
     *
     * @param  string  $name
     * @param  int  $seconds
     * @param  string|null  $owner
     * @return \Illuminate\Contracts\Cache\Lock
     */
    public function lock($name, $seconds = 0, $owner = null)
    {
        return new DynamoDbLock($this, $name, $seconds, $owner);
    }

    /**
     * Restore a lock instance using the owner identifier.
     *
     * @param  string  $name
     * @param  string  $owner
     * @return \Illuminate\Contracts\Cache\Lock
     */
    public function restoreLock($name, $owner)
    {
        return $this->lock($name, 0, $owner);
    }

    /**
     * Atomically refresh the expiration of a key if it matches the expected owner.
     *
     * @param  string  $key
     * @param  mixed  $expectedOwner
     * @param  int  $seconds
     * @return bool
     */
    public function refreshIfOwned($key, $expectedOwner, $seconds)
    {
        try {
            $this->dynamo->updateItem([
                'TableName' => $this->table,
                'Key' => [
                    $this->keyAttribute => [
                        'S' => $this->prefix.$key,
                    ],
                ],
                'ConditionExpression' => 'attribute_exists(#key) AND #value = :owner AND #expires_at > :now',
                'UpdateExpression' => 'SET #expires_at = :expires_at',
                'ExpressionAttributeNames' => [
                    '#key' => $this->keyAttribute,
                    '#value' => $this->valueAttribute,
                    '#expires_at' => $this->expirationAttribute,
                ],
                'ExpressionAttributeValues' => [
                    ':owner' => [
                        $this->type($expectedOwner) => $this->serialize($expectedOwner),
                    ],
                    ':now' => [
                        'N' => (string) $this->currentTime(),
                    ],
                    ':expires_at' => [
                        'N' => (string) $this->toTimestamp($seconds),
                    ],
                ],
            ]);

            return true;
        } catch (DynamoDbException $e) {
            if (str_contains($e->getMessage(), 'ConditionalCheckFailed')) {
                return false;
            }

            throw $e;
        }
    }

    /**
     * Remove an item from the cache.
     *
     * @param  string  $key
     * @return bool
     */
    public function forget($key)
    {
        $this->dynamo->deleteItem([
            'TableName' => $this->table,
            'Key' => [
                $this->keyAttribute => [
                    'S' => $this->prefix.$key,
                ],
            ],
        ]);

        return true;
    }

    /**
     * Remove all items from the cache.
     *
     * @return never
     *
     * @throws \RuntimeException
     */
    public function flush()
    {
        throw new RuntimeException('DynamoDb does not support flushing an entire table. Please create a new table.');
    }

    /**
     * Get the UNIX timestamp for the given number of seconds.
     *
     * @param  int  $seconds
     * @return int
     */
    protected function toTimestamp($seconds)
    {
        return $seconds > 0
            ? $this->availableAt($seconds)
            : $this->currentTime();
    }

    /**
     * Serialize the value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    protected function serialize($value)
    {
        return is_numeric($value) ? (string) $value : serialize($value);
    }

    /**
     * Unserialize the value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    protected function unserialize($value)
    {
        if (filter_var($value, FILTER_VALIDATE_INT) !== false) {
            return (int) $value;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        if ($this->serializableClasses !== null) {
            return unserialize($value, ['allowed_classes' => $this->serializableClasses]);
        }

        return unserialize($value);
    }

    /**
     * Get the DynamoDB type for the given value.
     *
     * @param  mixed  $value
     * @return string
     */
    protected function type($value)
    {
        return is_numeric($value) ? 'N' : 'S';
    }

    /**
     * Get the cache key prefix.
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Set the cache key prefix.
     *
     * @param  string  $prefix
     * @return void
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * Get the DynamoDb Client instance.
     *
     * @return \Aws\DynamoDb\DynamoDbClient
     */
    public function getClient()
    {
        return $this->dynamo;
    }
}
