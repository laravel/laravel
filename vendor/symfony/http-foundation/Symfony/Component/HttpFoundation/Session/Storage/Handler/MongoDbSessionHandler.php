<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Session\Storage\Handler;

/**
 * MongoDB session handler
 *
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class MongoDbSessionHandler implements \SessionHandlerInterface
{
    /**
     * @var \Mongo
     */
    private $mongo;

    /**
     * @var \MongoCollection
     */
    private $collection;

    /**
     * @var array
     */
    private $options;

    /**
     * Constructor.
     *
     * List of available options:
     *  * database: The name of the database [required]
     *  * collection: The name of the collection [required]
     *  * id_field: The field name for storing the session id [default: _id]
     *  * data_field: The field name for storing the session data [default: data]
     *  * time_field: The field name for storing the timestamp [default: time]
     *
     * @param \Mongo|\MongoClient $mongo   A MongoClient or Mongo instance
     * @param array               $options An associative array of field options
     *
     * @throws \InvalidArgumentException When MongoClient or Mongo instance not provided
     * @throws \InvalidArgumentException When "database" or "collection" not provided
     */
    public function __construct($mongo, array $options)
    {
        if (!($mongo instanceof \MongoClient || $mongo instanceof \Mongo)) {
            throw new \InvalidArgumentException('MongoClient or Mongo instance required');
        }

        if (!isset($options['database']) || !isset($options['collection'])) {
            throw new \InvalidArgumentException('You must provide the "database" and "collection" option for MongoDBSessionHandler');
        }

        $this->mongo = $mongo;

        $this->options = array_merge(array(
            'id_field'   => '_id',
            'data_field' => 'data',
            'time_field' => 'time',
        ), $options);
    }

    /**
     * {@inheritdoc}
     */
    public function open($savePath, $sessionName)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($sessionId)
    {
        $this->getCollection()->remove(array(
            $this->options['id_field'] => $sessionId,
        ));

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function gc($maxlifetime)
    {
        /* Note: MongoDB 2.2+ supports TTL collections, which may be used in
         * place of this method by indexing the "time_field" field with an
         * "expireAfterSeconds" option. Regardless of whether TTL collections
         * are used, consider indexing this field to make the remove query more
         * efficient.
         *
         * See: http://docs.mongodb.org/manual/tutorial/expire-data/
         */
        $time = new \MongoDate(time() - $maxlifetime);

        $this->getCollection()->remove(array(
            $this->options['time_field'] => array('$lt' => $time),
        ));

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function write($sessionId, $data)
    {
        $this->getCollection()->update(
            array($this->options['id_field'] => $sessionId),
            array('$set' => array(
                $this->options['data_field'] => new \MongoBinData($data, \MongoBinData::BYTE_ARRAY),
                $this->options['time_field'] => new \MongoDate(),
            )),
            array('upsert' => true, 'multiple' => false)
        );

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function read($sessionId)
    {
        $dbData = $this->getCollection()->findOne(array(
            $this->options['id_field'] => $sessionId,
        ));

        return null === $dbData ? '' : $dbData[$this->options['data_field']]->bin;
    }

    /**
     * Return a "MongoCollection" instance
     *
     * @return \MongoCollection
     */
    private function getCollection()
    {
        if (null === $this->collection) {
            $this->collection = $this->mongo->selectCollection($this->options['database'], $this->options['collection']);
        }

        return $this->collection;
    }

    /**
     * Return a Mongo instance
     *
     * @return \Mongo
     */
    protected function getMongo()
    {
        return $this->mongo;
    }
}
