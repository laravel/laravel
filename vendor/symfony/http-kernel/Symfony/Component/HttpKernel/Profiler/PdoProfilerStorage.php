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
 * Base PDO storage for profiling information in a PDO database.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Jan Schumann <js@schumann-it.com>
 */
abstract class PdoProfilerStorage implements ProfilerStorageInterface
{
    protected $dsn;
    protected $username;
    protected $password;
    protected $lifetime;
    protected $db;

    /**
     * Constructor.
     *
     * @param string  $dsn      A data source name
     * @param string  $username The username for the database
     * @param string  $password The password for the database
     * @param int     $lifetime The lifetime to use for the purge
     */
    public function __construct($dsn, $username = '', $password = '', $lifetime = 86400)
    {
        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;
        $this->lifetime = (int) $lifetime;
    }

    /**
     * {@inheritdoc}
     */
    public function find($ip, $url, $limit, $method, $start = null, $end = null)
    {
        if (null === $start) {
            $start = 0;
        }

        if (null === $end) {
            $end = time();
        }

        list($criteria, $args) = $this->buildCriteria($ip, $url, $start, $end, $limit, $method);

        $criteria = $criteria ? 'WHERE '.implode(' AND ', $criteria) : '';

        $db = $this->initDb();
        $tokens = $this->fetch($db, 'SELECT token, ip, method, url, time, parent FROM sf_profiler_data '.$criteria.' ORDER BY time DESC LIMIT '.((int) $limit), $args);
        $this->close($db);

        return $tokens;
    }

    /**
     * {@inheritdoc}
     */
    public function read($token)
    {
        $db = $this->initDb();
        $args = array(':token' => $token);
        $data = $this->fetch($db, 'SELECT data, parent, ip, method, url, time FROM sf_profiler_data WHERE token = :token LIMIT 1', $args);
        $this->close($db);
        if (isset($data[0]['data'])) {
            return $this->createProfileFromData($token, $data[0]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function write(Profile $profile)
    {
        $db = $this->initDb();
        $args = array(
            ':token'      => $profile->getToken(),
            ':parent'     => $profile->getParentToken(),
            ':data'       => base64_encode(serialize($profile->getCollectors())),
            ':ip'         => $profile->getIp(),
            ':method'     => $profile->getMethod(),
            ':url'        => $profile->getUrl(),
            ':time'       => $profile->getTime(),
            ':created_at' => time(),
        );

        try {
            if ($this->has($profile->getToken())) {
                $this->exec($db, 'UPDATE sf_profiler_data SET parent = :parent, data = :data, ip = :ip, method = :method, url = :url, time = :time, created_at = :created_at WHERE token = :token', $args);
            } else {
                $this->exec($db, 'INSERT INTO sf_profiler_data (token, parent, data, ip, method, url, time, created_at) VALUES (:token, :parent, :data, :ip, :method, :url, :time, :created_at)', $args);
            }
            $this->cleanup();
            $status = true;
        } catch (\Exception $e) {
            $status = false;
        }

        $this->close($db);

        return $status;
    }

    /**
     * {@inheritdoc}
     */
    public function purge()
    {
        $db = $this->initDb();
        $this->exec($db, 'DELETE FROM sf_profiler_data');
        $this->close($db);
    }

    /**
     * Build SQL criteria to fetch records by ip and url
     *
     * @param string $ip     The IP
     * @param string $url    The URL
     * @param string $start  The start period to search from
     * @param string $end    The end period to search to
     * @param string $limit  The maximum number of tokens to return
     * @param string $method The request method
     *
     * @return array An array with (criteria, args)
     */
    abstract protected function buildCriteria($ip, $url, $start, $end, $limit, $method);

    /**
     * Initializes the database
     *
     * @throws \RuntimeException When the requested database driver is not installed
     */
    abstract protected function initDb();

    protected function cleanup()
    {
        $db = $this->initDb();
        $this->exec($db, 'DELETE FROM sf_profiler_data WHERE created_at < :time', array(':time' => time() - $this->lifetime));
        $this->close($db);
    }

    protected function exec($db, $query, array $args = array())
    {
        $stmt = $this->prepareStatement($db, $query);

        foreach ($args as $arg => $val) {
            $stmt->bindValue($arg, $val, is_int($val) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
        }
        $success = $stmt->execute();
        if (!$success) {
            throw new \RuntimeException(sprintf('Error executing query "%s"', $query));
        }
    }

    protected function prepareStatement($db, $query)
    {
        try {
            $stmt = $db->prepare($query);
        } catch (\Exception $e) {
            $stmt = false;
        }

        if (false === $stmt) {
            throw new \RuntimeException('The database cannot successfully prepare the statement');
        }

        return $stmt;
    }

    protected function fetch($db, $query, array $args = array())
    {
        $stmt = $this->prepareStatement($db, $query);

        foreach ($args as $arg => $val) {
            $stmt->bindValue($arg, $val, is_int($val) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
        }
        $stmt->execute();
        $return = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $return;
    }

    protected function close($db)
    {
    }

    protected function createProfileFromData($token, $data, $parent = null)
    {
        $profile = new Profile($token);
        $profile->setIp($data['ip']);
        $profile->setMethod($data['method']);
        $profile->setUrl($data['url']);
        $profile->setTime($data['time']);
        $profile->setCollectors(unserialize(base64_decode($data['data'])));

        if (!$parent && !empty($data['parent'])) {
            $parent = $this->read($data['parent']);
        }

        if ($parent) {
            $profile->setParent($parent);
        }

        $profile->setChildren($this->readChildren($token, $profile));

        return $profile;
    }

    /**
     * Reads the child profiles for the given token.
     *
     * @param string $token  The parent token
     * @param string $parent The parent instance
     *
     * @return Profile[] An array of Profile instance
     */
    protected function readChildren($token, $parent)
    {
        $db = $this->initDb();
        $data = $this->fetch($db, 'SELECT token, data, ip, method, url, time FROM sf_profiler_data WHERE parent = :token', array(':token' => $token));
        $this->close($db);

        if (!$data) {
            return array();
        }

        $profiles = array();
        foreach ($data as $d) {
            $profiles[] = $this->createProfileFromData($d['token'], $d, $parent);
        }

        return $profiles;
    }

    /**
     * Returns whether data for the given token already exists in storage.
     *
     * @param string $token The profile token
     *
     * @return string
     */
    protected function has($token)
    {
        $db = $this->initDb();
        $tokenExists = $this->fetch($db, 'SELECT 1 FROM sf_profiler_data WHERE token = :token LIMIT 1', array(':token' => $token));
        $this->close($db);

        return !empty($tokenExists);
    }
}
