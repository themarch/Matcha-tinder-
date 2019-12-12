<?php
// ----- docker config
/*
  $DB_DSN = 'mysql:dbname=Matcha;host=mysql;port=3306';
  $DB_USER = 'root';
  $DB_PASSWORD = 'rootpass';
*/
$DB_DSN = 'mysql:dbname=Matcha;host=mysql;port=3306';
$DB_USER = 'root';
$DB_PASSWORD = 'rootpass';

// ---- docker config
// add this to docker-composer.yml => command: redis-server --requirepass yourpassword
/*
  $REDIS_HOST = 'redis';
  $REDIS_PWD = 'test1234';
*/

// windows command : config set requirepass test1234
$REDIS_HOST = 'redis';
$REDIS_PORT = 6379;
$REDIS_PASSWORD = 'test1234';
