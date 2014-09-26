<?php namespace Monashee\Backup\Commands;
/**
 * MonasheeBackup.php
 *
 * @author  Derek Marcinyshyn <derek@marcinyshyn.com>
 * @date    18/09/14
 */

use Illuminate\Console\Command;
use Illuminate\Events\Dispatcher;
use Monashee\Backup\Database;
use Monashee\Backup\Dump;
use Monashee\Backup\UploadS3;

class MonasheeBackup extends Command {

    /**
     * @var UploadS3
     */
    protected $uploadS3;

    /**
     * @var Dump
     */
    protected $dump;

    /**
     * @var Database
     */
    protected $database;

    /**
     * @var Dispatcher
     */
    protected $event;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'monashee:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup your MySQL databases to AWS S3.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Dispatcher $event, Database $database, Dump $dump, UploadS3 $uploadS3)
    {
        $this->event = $event;
        $this->database = $database;
        $this->dump = $dump;
        $this->uploadS3 = $uploadS3;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        try {
            $this->info('Monashee get databases...');
            $databases = $this->database->getDatabases();

            $this->info('Starting dumping databases...');
            $dump = $this->dump->backup($databases);

            if ($dump) {
                $this->info('Start uploading to AWS S3...');
                $uploadS3 = $this->uploadS3->uploadToS3($databases);
            }

            $this->event->fire('MonasheeBackupSuccess', compact('databases'));

        } catch (\Exception $e) {
            $this->event->fire('MonasheeBackupFail', $e->getTraceAsString());
        }
    }
} 