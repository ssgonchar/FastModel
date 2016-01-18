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
define('ROLE_GUEST', 1);

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

/**
 * Log
 */
define('LOG', 'yes');
define('APP_LOGS', 'logs/');

use SSGonchar\FastModel\SEMail\Email;

if (array_key_exists('user', $_SESSION)) {

}
$_SESSION = array(
    'user' => array(
        'id' => '4',
        'login' => 'SSGonchar',
        'role_id' => ROLE_GUEST,
    )
);

\SSGonchar\FastModel\SEModel\Model::setSession($_SESSION);

/** @var Model $model */
$email = new Email('emails');

var_dump($email);
var_dump($email->getList());
var_dump($email->get(3));