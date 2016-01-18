<?php

/**
 * Created by PhpStorm.
 * User: ssgonchar
 * Date: 18.01.2016
 * Time: 4:33
// */
//namespace SSGonchar\FastModel\Test\SEUtil\Db;
//
//use SSGonchar\FastModel\Test\SETestCase;
//use SSGonchar\FastModel\SEUtil\Db\QueryBuilder;
//use SSGonchar\FastModel\SEUtil\Db\DatabaseConnection;
//
//class QueryBuilderTest extends SETestCase
//{
//    /**
//     * @var QueryBuilder
//     */
//    private $queryBuilder;
//
//    /**
//     * @var DatabaseConnection
//     */
//    private $databaseConnection;
//
//    public function setUp()
//    {
//
//        $connectionSettings = array(
//            'dbhost' => APP_DBHOST,
//            'dbname' => APP_DBNAME,
//            'dbuser' => APP_DBUSER,
//            'dbpass' => APP_DBPASS,
//            'charset' => 'utf8_general_ci',
//        );
//
//        $this->queryBuilder = QueryBuilder::Create(
//            DatabaseConnection::Create($connectionSettings)
//        );
//    }
//
//    public function testCreate()
//    {
//        $this->assertInstanceOf('\SSGonchar\FastModel\SEUtil\Db\QueryBuilder', $this->queryBuilder);
//    }
//}
