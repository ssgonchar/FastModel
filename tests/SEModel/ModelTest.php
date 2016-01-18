<?php

/**
 * Created by PhpStorm.
 * User: ssgonchar
 * Date: 18.01.2016
 * Time: 4:30
 */
namespace SSGonchar\FastModel\Test\SEUtil;

use PHPUnit_Framework_TestCase;
use SSGonchar\FastModel\SEModel\Model;
use ReflectionClass;

class ModelTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Model
     */
    private $model;

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

        Model::setSession(array(
            'user' => array(
                'id' => '4',
                'login' => 'SSGonchar',
                'role_id' => ROLE_GUEST,
            ),
        ));

        $this->model = new Model('emails');
    }

    public function testConstruct()
    {
        $this->assertInstanceOf('\SSGonchar\FastModel\SEUtil\Db\Table', $this->model->table);
        $this->assertAttributeEquals(4, 'user_id', $this->model);
        $this->assertAttributeEquals('SSGonchar', 'user_login', $this->model);
        $this->assertAttributeEquals(ROLE_GUEST, 'user_role', $this->model);
    }

    public function testSelectList()
    {
        $result = $this->model->SelectList();
        $this->assertGreaterThan(0, count($result));
    }
}
