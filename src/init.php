<?php
session_start();
/**
 * Created by PhpStorm.
 * User: ssgonchar
 * Date: 17.01.2016
 * Time: 23:31
 */
require_once("../vendor/autoload.php");

/**
 * User roles
 */
define('ROLE_GUEST', 0);

/**
 * Localizations
 */
define('DEFAULT_LANG', 'en');

/**
 * DB connection settings
 */
define('APP_DBHOST', 'localhost');
define('APP_DBNAME', 'test');
define('APP_DBUSER', 'dev');
define('APP_DBPASS', '123456');
define('DB_TIME_ZONE', '0');
define('SLOW_QUERY_TIME', 1); // s
define('MAX_LENGTH_PER_PARAM', 100);

/**
 * Cache settings
 */
define('CACHE_ENABLED', 'yes');
define('MEMCACHE_HOST', 'localhost');
define('MEMCACHE_PORT', '11211');

use SSGonchar\FastModel\SEModel\Model;

/** @var Model $model */
$model = new Model('fm_emails');

$model->Select(1);

xdebug_var_dump($model);