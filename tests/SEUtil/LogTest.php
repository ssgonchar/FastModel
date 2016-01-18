<?php

/**
 * Created by PhpStorm.
 * User: ssgonchar
 * Date: 18.01.2016
 * Time: 4:31
 */
namespace SSGonchar\FastModel\Test\SEUtil;

use SSGonchar\FastModel\Test\SETestCase;
use SSGonchar\FastModel\SEUtil\Log;

class LogTest extends SETestCase
{
    private $logFile;

    public function setUp()
    {
        parent::setUp();
        $this->logFile = APP_LOGS . date('Ymd') . '.app.txt';
        if (!file_exists($this->logFile)) {
            mkdir(APP_LOGS, '0777');
        }
    }

    public function testCreate()
    {
        Log::Create(1, $this->logFile);

        $this->assertFileExists($this->logFile);
    }
}
