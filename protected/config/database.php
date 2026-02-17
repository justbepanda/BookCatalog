<?php

// This is the database connection configuration.
return array(
    'connectionString' => 'mysql:host=db;port=3306;dbname=' . getenv('DB_NAME'),
    'emulatePrepare' => true,
    'username' => getenv('DB_USER'),
    'password' => getenv('DB_PASSWORD'),
    'charset' => 'utf8',
);