<?php

/**
 * Created by PhpStorm.
 * User: ssgonchar
 * Date: 18.01.2016
 * Time: 4:32
 */
namespace SSGonchar\FastModel\Test\SEUtil\Db;

use SSGonchar\FastModel\SEUtil\Db\Table;

use SSGonchar\FastModel\Test\SETestCase;

class TableTest extends SETestCase
{
    /**
     * @var Table
     */
    private $table;
    private $tableName;

    public function setUp()
    {
        $this->tableName = "emails";

        $connectionSettings = array(
            'dbhost' => APP_DBHOST,
            'dbname' => APP_DBNAME,
            'dbuser' => APP_DBUSER,
            'dbpass' => APP_DBPASS,
            'charset' => 'utf8_general_ci',
        );

        $this->table = new Table($this->tableName, $connectionSettings);
    }

    public function testConnectDatabase()
    {
        $this->table->ConnectDatabase();

        $this->assertEquals(true, $this->table->is_connected);
        $this->assertInstanceOf('\SSGonchar\FastModel\SEUtil\Db\DatabaseConnection', $this->table->db);
        $this->assertInstanceOf('\SSGonchar\FastModel\SEUtil\Db\QueryBuilder', $this->table->query);
    }
}
