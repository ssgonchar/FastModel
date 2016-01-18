<?php
/**
 * Created by PhpStorm.
 * User: ssgonchar
 * Date: 18.01.2016
 * Time: 4:31
 */
namespace SSGonchar\FastModel\Test\SEUtil;

use SSGonchar\FastModel\Test\SETestCase;
use SSGonchar\FastModel\SEUtil\Timer;
use ReflectionClass;

/**
 * Class TimerTest
 * Tests for {@see \SSGonchar\FastModel\SEUtil\Timer}
 * @covers \SSGonchar\FastModel\SEUtil\Timer
 */
class TimerTest extends SETestCase
{
    /**
     * @var Timer
     */
    private $timer;

    public function setUp()
    {
        parent::setUp();
        $this->timer = Timer::Start();
    }

    /**
     *
     */
    public function testStart()
    {
        $this->assertInstanceOf('\SSGonchar\FastModel\SEUtil\Timer', $this->timer);
        $this->assertGreaterThan(0, count($this->timer->startTimes));
    }

    /**
     *
     */
    public function testGetMicrotime()
    {
        $microtime = $this->timer->GetMicrotime();
        $this->assertGreaterThan(0, $microtime);
    }

    /**
     *
     */
    public function testStartedAt()
    {
        $startedAt = $this->timer->StartedAt();
        $this->assertEquals($startedAt, $this->timer->startTimes[0]);
    }

    /**
     *
     */
    public function testCurrent()
    {
        $current = $this->timer->Current();

        $currentCalc = $this->timer->GetMicrotime() - $this->timer->startTimes[count($this->timer->startTimes) - 1];

        $this->assertEquals($current, $currentCalc);
    }

    /**
     *
     */
    /*
    public function testStop()
    {
        $this->timer->Start();
        $this->timer->GetMicrotime();
        $this->timer->GetMicrotime();
        $this->timer->Start();

        $stop = $this->timer->Stop();

        $stopCalc = $this->timer->GetMicrotime() - array_pop($this->timer->startTimes);

        $this->assertGreaterThan($stop, $stopCalc);
    }*/
}
