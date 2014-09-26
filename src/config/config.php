<?php

return [
    'BACKUP_MYSQL_HOST'     => getenv('BACKUP_MYSQL_HOST'),
    'BACKUP_MYSQL_USER'     => getenv('BACKUP_MYSQL_USER'),
    'BACKUP_MYSQL_PASSWORD' => getenv('BACKUP_MYSQL_PASSWORD'),
    'BACKUP_MYSQLDUMP_PATH' => getenv('BACKUP_MYSQLDUMP_PATH'),
    'BACKUP_AWS_KEY'        => getenv('BACKUP_AWS_KEY'),
    'BACKUP_AWS_SECRET'     => getenv('BACKUP_AWS_SECRET'),
    'BACKUP_S3_BUCKET'      => getenv('BACKUP_S3_BUCKET'),
    'BACKUP_S3_REGION'      => getenv('BACKUP_S3_REGION'),
    'BACKUP_STORAGE_PATH'   => getenv('BACKUP_STORAGE_PATH')
];