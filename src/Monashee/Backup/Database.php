<?php namespace Monashee\Backup;
/**
 * Database.php
 *
 * @author  Derek Marcinyshyn <derek@marcinyshyn.com>
 * @date    18/09/14
 */

use Illuminate\Events\Dispatcher;
use Illuminate\Config\Repository;

class Database {

    /**
     * @var \mysqli
     */
    private $connection;

    /**
     * @var Dispatcher
     */
    protected $event;

    /**
     * @var Repository
     */
    protected $config;

    /**
     * @param Dispatcher $event
     * @param Repository $config
     */
    public function __construct(Dispatcher $event, Repository $config)
    {
        $this->event = $event;
        $this->config = $config;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getDatabases()
    {
        if ($this->checkMysqlConnection()) {
            $query = 'SHOW DATABASES;';
            $result = mysqli_query($this->connection, $query);
            $databases = array();

            while ($row = mysqli_fetch_row($result)){
                if ($this->databasesNotBackedUp($row))
                    $databases[] = $row[0];
            }
            $result->close();

            if (empty($databases)) {
                $this->event->fire('MonasheeBackupError', 'Sorry no databases found.');
                throw new \Exception('Sorry no databases found.');
            }

            $this->event->fire('MonasheeBackupInfo', 'Done.');

            return $databases;
        } else {
            return false;
        }
    }

    /**
     * Check MySQL Connection
     *
     * @return bool
     * @throws \Exception
     */
    private function checkMysqlConnection() {
        $this->connection = new \mysqli(
            $this->config->get('backup::config')['BACKUP_MYSQL_HOST'],
            $this->config->get('backup::config')['BACKUP_MYSQL_USER'],
            $this->config->get('backup::config')['BACKUP_MYSQL_PASSWORD']);

        if ($this->connection->connect_errno) {
            $this->event->fire('MonasheeBackupError', 'No database connection');

            throw new \Exception('Error connecting to MySQL');
        }

        echo 'Connected: '.$this->connection->host_info."\n";
        echo 'Server info: '.$this->connection->get_server_info()."\n";

        return true;
    }

    /**
     * @param $row
     * @return bool
     */
    private function databasesNotBackedUp($row)
    {
        return
            ($row[0] != 'information_schema') &&
            ($row[0] != 'mysql') &&
            ($row[0] != 'performance_schema')
            ;
    }
} 