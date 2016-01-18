<?php
/**
 * Created by PhpStorm.
 * User: ssgonchar
 * Date: 18.01.2016
 * Time: 9:40
 */

namespace SSGonchar\FastModel\Test;

use PHPUnit_Framework_TestCase;

class SETestCase extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
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
        define('APP_LOGS', 'tests\logs\/');
    }
}