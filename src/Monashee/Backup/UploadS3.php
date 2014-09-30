<?php namespace Monashee\Backup;
/**
 * Class UploadS3
 * @package Monashee\Backup
 *
 * @author  Derek Marcinyshyn <derek@marcinyshyn.com>
 * @date    September 20, 2014
 */

use Illuminate\Events\Dispatcher;
use Illuminate\Config\Repository;
use Aws\S3\S3Client;
use League\Flysystem\File;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\AwsS3 as Adapter;

class UploadS3
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var S3Client
     */
    protected $client;

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
     * @param S3Client $client
     * @param Filesystem $filesystem
     * @param Adapter $adapter
     */
    public function __construct(Dispatcher $event, Repository $config)
    {
        $this->event = $event;
        $this->config = $config;
        $this->client = S3Client::factory(array(
            'key'       => $this->config->get('backup::config')['BACKUP_AWS_KEY'],
            'secret'    => $this->config->get('backup::config')['BACKUP_AWS_SECRET']
        ));
        $this->filesystem = new Filesystem(new Adapter($this->client, $this->config->get('backup::config')['BACKUP_S3_BUCKET']));
    }

    /**
     * @param $databases
     * @return bool
     */
    public function uploadToS3($databases)
    {
        $storagePath = storage_path($this->config->get('backup::config')['BACKUP_STORAGE_PATH']);

        foreach ($databases as $database)
        {
            echo 'uploading '.$database."...\n";

            $this->client->putObject(array(
                'Bucket'        => $this->config->get('backup::config')['BACKUP_S3_BUCKET'],
                'Key'           => $this->path($database),
                'SourceFile'    => $storagePath.$database.'-backup.sql.gz'
            ));

            echo 'done.'."\n";
        }

        $this->cleanUpDirectory($storagePath);
        $this->event->fire('MonasheeBackupInfo', 'done cleaning backup folder.');

        $this->deleteDailyBackups();
        $this->event->fire('MonasheeBackupInfo', 'done cleaning S3 folder.');

        return true;
    }

    /**
     * Delete AWS S3 directory of 2 months ago but keep 1st of the month
     */
    private function deleteDailyBackups()
    {
        try {
            $date = strtotime('-2 months');

            // leave first day of the month
            if ((int) date('j', $date) !== 1) {
                $this->filesystem->deleteDir(date("Y/m/d", $date));
            }
        } catch (\Exception $e) {
            $this->event->fire('MonasheeBackupError', 'Error trying to delete directory on AWS S3');
        }
    }

    /**
     * @param $database
     * @return string
     */
    private function path($database)
    {
        return date("Y/m/d/").$database.'-backup.sql.gz';
    }

    /**
     * @param $storagePath
     */
    private function cleanUpDirectory($storagePath)
    {
        try {
            $files = glob($storagePath.'*');

            foreach ($files as $file)
            {
                if (is_file($file)) {
                    unlink($file);
                    echo 'delete '.$file."\n";
                }
            }
        } catch (\Exception $e) {
            $this->event->fire('MonasheeBackupError', 'Error trying to delete backup files.');
        }
    }
}
