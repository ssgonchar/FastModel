<?php
/**
 * Created by PhpStorm.
 * User: ssgonchar
 * Date: 17.01.2016
 * Time: 23:31
 */
require_once("vendor/autoload.php");

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
define('APP_DBUSER', 'root');
define('APP_DBPASS', '123456');

/**
 * Cache settings
 */
define('CACHE_ENABLED', 'yes');
define('MEMCACHE_HOST', 'localhost');
define('MEMCACHE_PORT', '11211');

use SSGonchar\FastModel\SEModel\Model;

/** @var Model $model */
$model = new Model();