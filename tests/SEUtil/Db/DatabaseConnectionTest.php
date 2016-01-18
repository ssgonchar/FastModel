<?php

/**
 * Created by PhpStorm.
 * User: ssgonchar
 * Date: 18.01.2016
 * Time: 4:33
 */

namespace SSGonchar\FastModel\Test\SEUtil;

use SSGonchar\FastModel\Test\SETestCase;
use SSGonchar\FastModel\SEUtil\Db\DatabaseConnection;

class DatabaseConnectionTest extends SETestCase
{
    /**
     * @var DatabaseConnection
     */
    private $databaseConnection;

    public function setUp()
    {
        parent::setUp();

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
