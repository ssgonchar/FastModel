<?php

/**
 * Created by PhpStorm.
 * User: ssgonchar
 * Date: 18.01.2016
 * Time: 4:30
 */
namespace SSGonchar\FastModel\Test\SEUtil;

use SSGonchar\FastModel\Test\SETestCase;
use SSGonchar\FastModel\SEModel\Model;
use ReflectionClass;

class ModelTest extends SETestCase
{
    /**
     * @var Model
     */
    private $model;
    private $user_id;
    private $user_login;
    private $user_role_id;

    public function setUp()
    {
        parent::setUp();

        $this->user_id = 10;
        $this->user_login = 'ssgonchar';
        $this->user_role_id = 777;

        Model::setSession(array(
            'user' => array(
                'id' => $this->user_id,
                'login' => $this->user_login,
                'role_id' => $this->user_role_id,
            ),
        ));

        $this->model = new Model('emails');
    }

    public function testConstruct()
    {
        $this->assertInstanceOf('\SSGonchar\FastModel\SEUtil\Db\Table', $this->model->table);
        $this->assertAttributeEquals($this->user_id, 'user_id', $this->model);
        $this->assertAttributeEquals($this->user_login, 'user_login', $this->model);
        $this->assertAttributeEquals($this->user_role_id, 'user_role', $this->model);
    }

    /*
    public function testSelectList()
    {
        $result = $this->model->SelectList();
        $this->assertGreaterThan(0, count($result));
    }*/
}
