<?php

/**
 * Created by PhpStorm.
 * User: ssgonchar
 * Date: 18.01.2016
 * Time: 4:33
 */

namespace SSGonchar\FastModel\Test\SEUtil;

use PHPUnit_Framework_TestCase;
use SSGonchar\FastModel\SEUtil\Db\DatabaseConnection;

class DatabaseConnectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var DatabaseConnection
     */
    private $databaseConnection;

    public function setUp()
    {
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

        $connectionSettings = array(
            'dbhost' => APP_DBHOST,
            'dbname' => APP_DBNAME,
            'dbuser' => APP_DBUSER,
            'dbpass' => APP_DBPASS,
            'charset' => 'utf8_general_ci',
        );

        $this->databaseConnection = DatabaseConnection::Create($connectionSettings);
    }

    public function testConstruct()
    {
        $this->assertInstanceOf('\SSGonchar\FastModel\SEUtil\Db\DatabaseConnection', $this->databaseConnection);
    }
}
