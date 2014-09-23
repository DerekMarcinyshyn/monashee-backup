<?php namespace Monashee\Backup;
/**
 * Dump.php
 *
 * @author  Derek Marcinyshyn <derek@marcinyshyn.com>
 * @date    19/09/14
 */

use Illuminate\Events\Dispatcher;
use Illuminate\Config\Repository;

class Dump {

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
    public function __construct(Dispatcher $event, Repository $config){
        $this->event = $event;
        $this->config = $config;
    }

    /**
     * Create backups and save to file
     *
     * @param $databases
     * @return bool
     * @throws \Exception
     */
    public function backup($databases)
    {
        foreach ($databases as $database)
        {
            echo "\n".'dumping '.$database.' database'."\n";

            try {
                system($this->command($database));
            } catch (\Exception $e) {
                $this->event->fire('MonasheeBackupError', $e->getMessage());
                throw new \Exception('mysqldump error');
            }

            echo 'done.'."\n\n";
        }

        $this->event->fire('MonasheeBackupInfo', 'All databases done dumping.');

        return true;
    }

    /**
     * Build command for system()
     *
     * @param $database
     * @return string
     */
    private function command($database)
    {
        $mysqldumpPath = $this->config->get('backup::config')['BACKUP_MYSQLDUMP_PATH'];
        $mysqldumpOptions = ' \
            --quote-names \
            --quick \
            --add-drop-table \
            --add-locks \
            --allow-keywords \
            --disable-keys \
            --extended-insert \
            --single-transaction \
            --create-options \
            --comments \
            --net_buffer_length=16384';
        $host = $this->config->get('backup::config')['BACKUP_MYSQL_HOST'];
        $user = $this->config->get('backup::config')['BACKUP_MYSQL_USER'];
        $password = $this->config->get('backup::config')['BACKUP_MYSQL_PASSWORD'];
        $folder = storage_path($this->config->get('backup::config')['BACKUP_STORAGE_PATH']);
        $file = $database.'-backup.sql.gz';

        return $mysqldumpPath.$mysqldumpOptions.' --host='.$host.' --user='.$user.' --password='.$password.' '.
            $database.' | gzip > '.$folder.$file;
    }
} 