<?php
/**
 * Created by PhpStorm.
 * User: ssgonchar
 * Date: 18.01.2016
 * Time: 4:21
 */

namespace SSGonchar\FastModel\SEUtil;

/**
 * Class Timer
 * @package SSGonchar\FastModel\SEUtil
 */
class Timer
{
    /**
     *
     *
     * @var array
     */
    var $startTimes;

    /**
     *
     */
    private function __construct()
    {
        $this->startTimes = array();
    }

    /**
     *
     *
     *
     *
     * @return Timer
     */
    public static function Start()
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new Timer();
        }

        $time = $instance->GetMicrotime();
        array_push($instance->startTimes, $time);

        return $instance;
    }

    /**
     *
     *
     *
     *
     * @return float
     */
    function StartedAt()
    {
        return $this->startTimes[0];
    }

    /**
     *
     *
     *
     *
     * @return float
     */
    function Current()
    {
        return $this->GetMicrotime() - $this->startTimes[count($this->startTimes) - 1];
    }

    /**
     *
     *
     *
     *
     * @return float
     */
    function Stop()
    {
        return $this->GetMicrotime() - array_pop($this->startTimes);
    }

    /**
     *
     *
     *
     *
     * @return float
     */
    function GetMicrotime()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }
}