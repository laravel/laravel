<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Profiler;

/**
 * RedisProfilerStorage stores profiling information in Redis.
 *
 * @author Andrej Hudec <pulzarraider@gmail.com>
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class RedisProfilerStorage implements ProfilerStorageInterface
{
    const TOKEN_PREFIX = 'sf_profiler_';

    const REDIS_OPT_SERIALIZER = 1;
    const REDIS_OPT_PREFIX = 2;
    const REDIS_SERIALIZER_NONE = 0;
    const REDIS_SERIALIZER_PHP = 1;

    protected $dsn;
    protected $lifetime;

    /**
     * @var \Redis
     */
    private $redis;

    /**
     * Constructor.
     *
     * @param string $dsn      A data source name
     * @param string $username Not used
     * @param string $password Not used
     * @param int    $lifetime The lifetime to use for the purge
     */
    public function __construct($dsn, $username = '', $password = '', $lifetime = 86400)
    {
        $this->dsn = $dsn;
        $this->lifetime = (int) $lifetime;
    }

    /**
     * {@inheritdoc}
     */
    public function find($ip, $url, $limit, $method, $start = null, $end = null)
    {
        $indexName = $this->getIndexName();

        if (!$indexContent = $this->getValue($indexName, self::REDIS_SERIALIZER_NONE)) {
            return array();
        }

        $profileList = array_reverse(explode("\n", $indexContent));
        $result = array();

        foreach ($profileList as $item) {
            if ($limit === 0) {
                break;
            }

            if ($item == '') {
                continue;
            }

            list($itemToken, $itemIp, $itemMethod, $itemUrl, $itemTime, $itemParent) = explode("\t", $item, 6);

            $itemTime = (int) $itemTime;

            if ($ip && false === strpos($itemIp, $ip) || $url && false === strpos($itemUrl, $url) || $method && false === strpos($itemMethod, $method)) {
                continue;
            }

            if (!empty($start) && $itemTime < $start) {
                continue;
            }

            if (!empty($end) && $itemTime > $end) {
                continue;
            }

            $result[] = array(
                'token'  => $itemToken,
                'ip'     => $itemIp,
                'method' => $itemMethod,
                'url'    => $itemUrl,
                'time'   => $itemTime,
                'parent' => $itemParent,
            );
            --$limit;
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function purge()
    {
        // delete only items from index
        $indexName = $this->getIndexName();

        $indexContent = $this->getValue($indexName, self::REDIS_SERIALIZER_NONE);

        if (!$indexContent) {
            return false;
        }

        $profileList = explode("\n", $indexContent);

        $result = array();

        foreach ($profileList as $item) {
            if ($item == '') {
                continue;
            }

            if (false !== $pos = strpos($item, "\t")) {
                $result[] = $this->getItemName(substr($item, 0, $pos));
            }
        }

        $result[] = $indexName;

        return $this->delete($result);
    }

    /**
     * {@inheritdoc}
     */
    public function read($token)
    {
        if (empty($token)) {
            return false;
        }

        $profile = $this->getValue($this->getItemName($token), self::REDIS_SERIALIZER_PHP);

        if (false !== $profile) {
            $profile = $this->createProfileFromData($token, $profile);
        }

        return $profile;
    }

    /**
     * {@inheritdoc}
     */
    public function write(Profile $profile)
    {
        $data = array(
            'token'    => $profile->getToken(),
            'parent'   => $profile->getParentToken(),
            'children' => array_map(function ($p) { return $p->getToken(); }, $profile->getChildren()),
            'data'     => $profile->getCollectors(),
            'ip'       => $profile->getIp(),
            'method'   => $profile->getMethod(),
            'url'      => $profile->getUrl(),
            'time'     => $profile->getTime(),
        );

        $profileIndexed = false !== $this->getValue($this->getItemName($profile->getToken()));

        if ($this->setValue($this->getItemName($profile->getToken()), $data, $this->lifetime, self::REDIS_SERIALIZER_PHP)) {
            if (!$profileIndexed) {
                // Add to index
                $indexName = $this->getIndexName();

                $indexRow = implode("\t", array(
                    $profile->getToken(),
                    $profile->getIp(),
                    $profile->getMethod(),
                    $profile->getUrl(),
                    $profile->getTime(),
                    $profile->getParentToken(),
                ))."\n";

                return $this->appendValue($indexName, $indexRow, $this->lifetime);
            }

            return true;
        }

        return false;
    }

    /**
     * Internal convenience method that returns the instance of Redis.
     *
     * @return \Redis
     *
     * @throws \RuntimeException
     */
    protected function getRedis()
    {
        if (null === $this->redis) {
            $data = parse_url($this->dsn);

            if (false === $data || !isset($data['scheme']) || $data['scheme'] !== 'redis' || !isset($data['host']) || !isset($data['port'])) {
                throw new \RuntimeException(sprintf('Please check your configuration. You are trying to use Redis with an invalid dsn "%s". The minimal expected format is "redis://[host]:port".', $this->dsn));
            }

            if (!extension_loaded('redis')) {
                throw new \RuntimeException('RedisProfilerStorage requires that the redis extension is loaded.');
            }

            $redis = new \Redis();
            $redis->connect($data['host'], $data['port']);

            if (isset($data['path'])) {
                $redis->select(substr($data['path'], 1));
            }

            if (isset($data['pass'])) {
                $redis->auth($data['pass']);
            }

            $redis->setOption(self::REDIS_OPT_PREFIX, self::TOKEN_PREFIX);

            $this->redis = $redis;
        }

        return $this->redis;
    }

    /**
     * Set instance of the Redis
     *
     * @param \Redis $redis
     */
    public function setRedis($redis)
    {
        $this->redis = $redis;
    }

    private function createProfileFromData($token, $data, $parent = null)
    {
        $profile = new Profile($token);
        $profile->setIp($data['ip']);
        $profile->setMethod($data['method']);
        $profile->setUrl($data['url']);
        $profile->setTime($data['time']);
        $profile->setCollectors($data['data']);

        if (!$parent && $data['parent']) {
            $parent = $this->read($data['parent']);
        }

        if ($parent) {
            $profile->setParent($parent);
        }

        foreach ($data['children'] as $token) {
            if (!$token) {
                continue;
            }

            if (!$childProfileData = $this->getValue($this->getItemName($token), self::REDIS_SERIALIZER_PHP)) {
                continue;
            }

            $profile->addChild($this->createProfileFromData($token, $childProfileData, $profile));
        }

        return $profile;
    }

    /**
     * Gets the item name.
     *
     * @param string $token
     *
     * @return string
     */
    private function getItemName($token)
    {
        $name = $token;

        if ($this->isItemNameValid($name)) {
            return $name;
        }

        return false;
    }

    /**
     * Gets the name of the index.
     *
     * @return string
     */
    private function getIndexName()
    {
        $name = 'index';

        if ($this->isItemNameValid($name)) {
            return $name;
        }

        return false;
    }

    private function isItemNameValid($name)
    {
        $length = strlen($name);

        if ($length > 2147483648) {
            throw new \RuntimeException(sprintf('The Redis item key "%s" is too long (%s bytes). Allowed maximum size is 2^31 bytes.', $name, $length));
        }

        return true;
    }

    /**
     * Retrieves an item from the Redis server.
     *
     * @param string $key
     * @param int    $serializer
     *
     * @return mixed
     */
    private function getValue($key, $serializer = self::REDIS_SERIALIZER_NONE)
    {
        $redis = $this->getRedis();
        $redis->setOption(self::REDIS_OPT_SERIALIZER, $serializer);

        return $redis->get($key);
    }

    /**
     * Stores an item on the Redis server under the specified key.
     *
     * @param string $key
     * @param mixed  $value
     * @param int    $expiration
     * @param int    $serializer
     *
     * @return bool
     */
    private function setValue($key, $value, $expiration = 0, $serializer = self::REDIS_SERIALIZER_NONE)
    {
        $redis = $this->getRedis();
        $redis->setOption(self::REDIS_OPT_SERIALIZER, $serializer);

        return $redis->setex($key, $expiration, $value);
    }

    /**
     * Appends data to an existing item on the Redis server.
     *
     * @param string $key
     * @param string $value
     * @param int    $expiration
     *
     * @return bool
     */
    private function appendValue($key, $value, $expiration = 0)
    {
        $redis = $this->getRedis();
        $redis->setOption(self::REDIS_OPT_SERIALIZER, self::REDIS_SERIALIZER_NONE);

        if ($redis->exists($key)) {
            $redis->append($key, $value);

            return $redis->setTimeout($key, $expiration);
        }

        return $redis->setex($key, $expiration, $value);
    }

    /**
     * Removes the specified keys.
     *
     * @param array $keys
     *
     * @return bool
     */
    private function delete(array $keys)
    {
        return (bool) $this->getRedis()->delete($keys);
    }
}
