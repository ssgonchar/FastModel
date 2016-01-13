<?php
/**
 * Created by PhpStorm.
 * User: ssgonchar
 * Date: 14.01.2016
 * Time: 1:20
 */

namespace SSGonchar\FastModel;

class Cache
{
    public function __construct()
    {

    }

    public function get()
    {
        //...get key value...
    }

    public function set()
    {
        //...set key value...
    }

    private function connect()
    {
        //...connect to memcache...
    }

    public function delete()
    {
        //...unset key...
    }

    public function flush()
    {
        //...clear all cache...
    }
}