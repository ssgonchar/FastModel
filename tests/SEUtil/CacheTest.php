<?php

/**
 * Created by PhpStorm.
 * User: ssgonchar
 * Date: 18.01.2016
 * Time: 4:32
 */

namespace SSGonchar\FastModel\Test\SEUtil;

use SSGonchar\FastModel\Test\SETestCase;
use SSGonchar\FastModel\SEUtil\Cache;

class CacheTest extends SETestCase
{
    /**
     * @var Cache
     */
    private $cache;

    public function setUp()
    {
        parent::setUp();

        $this->cache = Cache::Create();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf('\Memcache', $this->cache->connection);
        $this->assertInstanceOf('\SSGonchar\FastModel\SEUtil\Cache', $this->cache);
    }
}
