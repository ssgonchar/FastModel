<?php
/**
 * Created by PhpStorm.
 * User: ssgonchar
 * Date: 18.01.2016
 * Time: 4:31
 */
namespace SSGonchar\FastModel\Test\SEUtil;

use PHPUnit_Framework_TestCase;
use SSGonchar\FastModel\SEUtil\Timer;
use ReflectionClass;

/**
 * Class TimerTest
 * Tests for {@see \SSGonchar\FastModel\SEUtil\Timer}
 * @covers \SSGonchar\FastModel\SEUtil\Timer
 */
class TimerTest extends PHPUnit_Framework_TestCase
{

    /**
     *
     */
    public function testStart()
    {
        $timer = Timer::Start();
        $this->assertInstanceOf('\SSGonchar\FastModel\SEUtil\Timer', $timer);
    }

    /**
     *
     */
    public function testStartedAt()
    {

    }

    /**
     *
     */
    public function testCurrent()
    {

    }

    /**
     *
     */
    public function testStop()
    {

    }

    /**
     *
     */
    public function testGetMicrotime()
    {

    }
}
