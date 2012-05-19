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
 * PdoSessionHandler.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Michael Williams <michael.williams@funsational.com>
 */
class PdoSessionHandler implements \SessionHandlerInterface
{
    /**
     * PDO instance.
     *
     * @var \PDO
     */
    private $pdo;

    /**
     * Database options.
     *
     *
     * @var array
     */
    private $dbOptions;

    /**
     * Constructor.
     *
     * @param \PDO  $pdo       A \PDO instance
     * @param array $dbOptions An associative array of DB options
     * @param array $options   Session configuration options
     *
     * @throws \InvalidArgumentException When "db_table" option is not provided
     */
    public function __construct(\PDO $pdo, array $dbOptions = array(), array $options = array())
    {
        if (!array_key_exists('db_table', $dbOptions)) {
            throw new \InvalidArgumentException('You must provide the "db_table" option for a PdoSessionStorage.');
        }

        $this->pdo = $pdo;
        $this->dbOptions = array_merge(array(
            'db_id_col'   => 'sess_id',
            'db_data_col' => 'sess_data',
            'db_time_col' => 'sess_time',
        ), $dbOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function open($path, $name)
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
    public function destroy($id)
    {
        // get table/column
        $dbTable  = $this->dbOptions['db_table'];
        $dbIdCol = $this->dbOptions['db_id_col'];

        // delete the record associated with this id
        $sql = "DELETE FROM $dbTable WHERE $dbIdCol = :id";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, \PDO::PARAM_STR);
            $stmt->execute();
        } catch (\PDOException $e) {
            throw new \RuntimeException(sprintf('PDOException was thrown when trying to manipulate session data: %s', $e->getMessage()), 0, $e);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function gc($lifetime)
    {
        // get table/column
        $dbTable    = $this->dbOptions['db_table'];
        $dbTimeCol = $this->dbOptions['db_time_col'];

        // delete the session records that have expired
        $sql = "DELETE FROM $dbTable WHERE $dbTimeCol < (:time - $lifetime)";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':time', time(), \PDO::PARAM_INT);
            $stmt->execute();
        } catch (\PDOException $e) {
            throw new \RuntimeException(sprintf('PDOException was thrown when trying to manipulate session data: %s', $e->getMessage()), 0, $e);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function read($id)
    {
        // get table/columns
        $dbTable    = $this->dbOptions['db_table'];
        $dbDataCol = $this->dbOptions['db_data_col'];
        $dbIdCol   = $this->dbOptions['db_id_col'];

        try {
            $sql = "SELECT $dbDataCol FROM $dbTable WHERE $dbIdCol = :id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, \PDO::PARAM_STR);

            $stmt->execute();
            // it is recommended to use fetchAll so that PDO can close the DB cursor
            // we anyway expect either no rows, or one row with one column. fetchColumn, seems to be buggy #4777
            $sessionRows = $stmt->fetchAll(\PDO::FETCH_NUM);

            if (count($sessionRows) == 1) {
                return base64_decode($sessionRows[0][0]);
            }

            // session does not exist, create it
            $this->createNewSession($id);

            return '';
        } catch (\PDOException $e) {
            throw new \RuntimeException(sprintf('PDOException was thrown when trying to read the session data: %s', $e->getMessage()), 0, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function write($id, $data)
    {
        // get table/column
        $dbTable   = $this->dbOptions['db_table'];
        $dbDataCol = $this->dbOptions['db_data_col'];
        $dbIdCol   = $this->dbOptions['db_id_col'];
        $dbTimeCol = $this->dbOptions['db_time_col'];

        $sql = ('mysql' === $this->pdo->getAttribute(\PDO::ATTR_DRIVER_NAME))
            ? "INSERT INTO $dbTable ($dbIdCol, $dbDataCol, $dbTimeCol) VALUES (:id, :data, :time) "
              ."ON DUPLICATE KEY UPDATE $dbDataCol = VALUES($dbDataCol), $dbTimeCol = CASE WHEN $dbTimeCol = :time THEN (VALUES($dbTimeCol) + 1) ELSE VALUES($dbTimeCol) END"
            : "UPDATE $dbTable SET $dbDataCol = :data, $dbTimeCol = :time WHERE $dbIdCol = :id";

        try {
            //session data can contain non binary safe characters so we need to encode it
            $encoded = base64_encode($data);
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, \PDO::PARAM_STR);
            $stmt->bindParam(':data', $encoded, \PDO::PARAM_STR);
            $stmt->bindValue(':time', time(), \PDO::PARAM_INT);
            $stmt->execute();

            if (!$stmt->rowCount()) {
                // No session exists in the database to update. This happens when we have called
                // session_regenerate_id()
                $this->createNewSession($id, $data);
            }
        } catch (\PDOException $e) {
            throw new \RuntimeException(sprintf('PDOException was thrown when trying to write the session data: %s', $e->getMessage()), 0, $e);
        }

        return true;
    }

    /**
     * Creates a new session with the given $id and $data
     *
     * @param string $id
     * @param string $data
     *
     * @return boolean True.
     */
    private function createNewSession($id, $data = '')
    {
        // get table/column
        $dbTable    = $this->dbOptions['db_table'];
        $dbDataCol = $this->dbOptions['db_data_col'];
        $dbIdCol   = $this->dbOptions['db_id_col'];
        $dbTimeCol = $this->dbOptions['db_time_col'];

        $sql = "INSERT INTO $dbTable ($dbIdCol, $dbDataCol, $dbTimeCol) VALUES (:id, :data, :time)";

        //session data can contain non binary safe characters so we need to encode it
        $encoded = base64_encode($data);
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, \PDO::PARAM_STR);
        $stmt->bindParam(':data', $encoded, \PDO::PARAM_STR);
        $stmt->bindValue(':time', time(), \PDO::PARAM_INT);
        $stmt->execute();

        return true;
    }
}
