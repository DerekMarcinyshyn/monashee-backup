# Monashee Backup

[![Build Status](https://travis-ci.org/DerekMarcinyshyn/monashee-backup.svg?branch=master)](https://travis-ci.org/DerekMarcinyshyn/monashee-backup)

A simple Laravel artisan utility to backup MySQL database to AWS S3 bucket.

## Configuration

Add this line to the providers array in your ```app/config/app.php``` file:
```php
'Monashee\Backup\BackupServiceProvider',
```

Create or edit your ```.env.php``` file in your root directory. Copy the config settings and edit the fields.

```php
<?php

return [
    'BACKUP_MYSQL_HOST'     => 'host',
    'BACKUP_MYSQL_USER'     => 'user',
    'BACKUP_MYSQL_PASSWORD' => 'password',
    'BACKUP_MYSQLDUMP_PATH' => '/usr/bin/mysqldump',
    'BACKUP_AWS_KEY'        => 'key',
    'BACKUP_AWS_SECRET'     => 'secret',
    'BACKUP_S3_BUCKET'      => 'bucket',
    'BACKUP_S3_REGION'      => 'us-west-2',
    'BACKUP_EMAIL'          => 'name@example.com',
    'BACKUP_SEND_EMAILS'    => true,
    'BACKUP_STORAGE_PATH'   => 'monashee/backup/'
];
```

AWS S3 Regions http://docs.aws.amazon.com/general/latest/gr/rande.html#s3_region


## Usage

```php
php artisan monashee:backup
```

Test in console to make sure it runs without errors. I find it easier to create a MySQL user called backup and add that user to all of the databases I want to backup.

Set a cron job to trigger the command when needed.


## Notes

It uses the mysqldump command for creating the backup. [More info mysqldump](http://dev.mysql.com/doc/refman/5.1/en/mysqldump.html)

mysqldump requires at least the SELECT privilege for dumped tables, SHOW VIEW for dumped views, TRIGGER for dumped triggers, and LOCK TABLES if the --single-transaction option is not used. Certain options might require other privileges as noted in the option descriptions.

To reload a dump file, you must have the same privileges needed to create each of the dumped objects by issuing CREATE statements manually.

\Monashee\Backup\Dump.php has the mysqldump options
