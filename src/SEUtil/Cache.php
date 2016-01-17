<?php
/**
     * Created by PhpStorm.
     * User: ssgonchar
     * Date: 18.01.2016
     * Time: 1:29
     */

namespace SSGonchar\FastModel\SEUtil;

define('CACHE_LIFETIME_TAG', 1209600); //14d
define('CACHE_LIFETIME_LONG', 86400); //1d
define('CACHE_LIFETIME_STANDARD', 10800); //3h
define('CACHE_LIFETIME_ONLINE', 600); //10m
define('CACHE_LIFETIME_SHORT', 300); //5m
define('CACHE_LIFETIME_30S', 30); //30s
define('CACHE_LIFETIME_MIN', 60); //1m
define('CACHE_LIFETIME_LOCK', 10); //10s

define('CACHE_TAG_PREFIX', 'ct-');
define('CACHE_LOCK_PREFIX', 'l-');

define('CACHE_LOG', 'no');

/**
 * Class Cache
 * @package SSGonchar\FastModel\SEUtil
 */
class Cache
{
    var $connection;

    /**
     * @param array $connection_settings
     * @see DatabaseConnection::$db
     */
    public function __construct($connection_settings)
    {
        if (CACHE_ENABLED == 'no') {
            return;
        }
        $this->_connect($connection_settings);
    }

    /**
     * @param $connection_settings
     * @return bool
     */
    function _connect($connection_settings)
    {
        $this->connection = new \Memcache();
        $is_connected = @$this->connection->connect($connection_settings['host'], $connection_settings['port']);

        if (!$is_connected) {
            $this->connection = null;
        }

        return $is_connected;
    }

    /**
     * @return Cache
     * @internal param array $connection_settings
     * @see Cache::_connect()
     */
    public static function & Create()
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new Cache(array('host' => MEMCACHE_HOST, 'port' => MEMCACHE_PORT));
        }
        return $instance;
    }

    /**
     * @param $key
     * @param $value
     * @param array $tag_names
     * @param int $lifetime
     * @return bool
     */
    public static function SetData($key, $value, $tag_names = array(), $lifetime = CACHE_LIFETIME_STANDARD)
    {
        if (CACHE_ENABLED == 'no') {
            return false;
        }
        // 2010.11.01, zharkov:
        $cache = Cache::Create();
        return $cache->_set_data($key, $value, $tag_names, $lifetime);
    }

    /**
     * @param $key
     * @param $value
     * @param $tag_names
     * @param integer $lifetime
     * @return bool
     */
    function _set_data($key, $value, $tag_names, $lifetime)
    {
        $values = array();
        $values['data'] = $value;

        if (!empty($tag_names) && is_array($tag_names)) {
            $tag_values = $this->connection->get($tag_names);

            $tags = array();
            foreach ($tag_names as $tag) {
                $tag = CACHE_TAG_PREFIX . $tag;

                if (isset($tag_values[$tag])) {
                    $tags[$tag] = $tag_values[$tag];
                } else {
                    $time = $this->_get_key($tag);
                    if (empty($time)) {
                        $time = time();
                        $this->_set_key($tag, $time, CACHE_LIFETIME_TAG);
                    }

                    $tags[$tag] = $time;
                }
            }

            $values['tags'] = $tags;
        }

        if (CACHE_LOG == 'yes') {
            Log::AddLine(LOG_CACHE, "set: \t" . var_export($key, true) . "\n" . var_export($values, true));
        }

        return $this->_set_key($key, $values, $lifetime);
    }

    /**
     * @param $key
     * @param int $lifetime
     * @return bool
     */
    public static function SetLock($key, $lifetime = CACHE_LIFETIME_LOCK)
    {
        //print_r($key);
        if (CACHE_ENABLED == 'no') {
            return true;
        }
        // 2010.11.01, zharkov:

        $cache = Cache::Create();
        return $cache->_add_key(CACHE_LOCK_PREFIX . $key, true, $lifetime);
    }

    /**
     * @param string $key
     * @param boolean $value
     * @param integer $lifetime
     * @return bool
     */
    function _add_key($key, $value, $lifetime)
    {
        if (CACHE_PREFIX !== '') {
            $key = CACHE_PREFIX . $key;
        }

        if (empty($this->connection)) {
            return false;
        }

        //if (!defined('ENABLE_CACHE') OR ENABLE_CACHE != 'yes') return false;
        if (CACHE_LOG == 'yes') {
            Log::AddLine(LOG_CACHE, "add: \t" . $key . ':' . $lifetime);
        }

        return $this->connection->add($key, $value, false, $lifetime);
    }

    /**
     * @param $key
     * @param $value
     * @param $lifetime
     * @return bool
     */
    function _set_key($key, $value, $lifetime)
    {
        if (CACHE_PREFIX !== '') {
            $key = CACHE_PREFIX . $key;
        }
        if (empty($this->connection)) {
            return false;
        }
        if (CACHE_LOG == 'yes') {
            Log::AddLine(LOG_CACHE, "set: \t" . $key . ':' . $lifetime);
        }

        return $this->connection->set($key, $value, false, $lifetime);
    }

    /**
     * @param $key
     * @return array|null
     */
    public static function GetData($key)
    {
        if (CACHE_ENABLED == 'no') {
            return null;
        }
        // 2010.11.01, zharkov:

        $cache = Cache::Create();
        return $cache->_get_data($key);
    }

    /**
     * @param $key
     * @return bool
     */
    public static function IsLocked($key)
    {
        if (CACHE_ENABLED == 'no') {
            return false;
        }
        // 2010.11.01, zharkov:

        $cache = Cache::Create();
        return $cache->_get_key(CACHE_LOCK_PREFIX . $key);
    }

    /**
     * @param $key
     * @return array|null
     */
    function _get_data($key)
    {
        $result = $this->_get_key($key);

        if (empty($result)) {
            return null;
        }

        $rowset = array();
        $rowset['data'] = isset($result['data']) ? $result['data'] : null;

        if (isset($result['tags'])) {
            $cachetags = $result['tags'];
            $tags = $this->_get_key(array_keys($cachetags));

            $outdated = false;
            foreach ($cachetags as $tag => $value) {
                $tag = CACHE_PREFIX !== '' ? CACHE_PREFIX . $tag : $tag;
                if (!isset($tags[$tag]) || $tags[$tag] != $value) {
                    $outdated = true;
                    break;
                }
            }

            if ($outdated) {
                $rowset['outdated'] = true;
            }
        }

        if (CACHE_LOG == 'yes') {
            Log::AddLine(LOG_CACHE, "get: \t" . var_export($key, true) . "\n" . var_export($result, true));
        }

        return $rowset;
    }

    /**
     * @param $key
     * @return bool
     */
    function _get_key($key)
    {
        /*$this->ClearKey($key);*/
        if (CACHE_PREFIX !== '') {
            if (!is_array($key)) {
                $key = CACHE_PREFIX . $key;
            } else {
                for ($i = 0; $i < count($key); $i++) {
                    $key[$i] = CACHE_PREFIX . $key[$i];
                }
            }
        }

        if (empty($this->connection)) {
            return false;
        }

        $result = $this->connection->get($key);

        if (CACHE_LOG == 'yes') {
                    if (is_array($key)) {
                foreach ($key as $k) {
                    if (!isset($result[$k]))
                        Log::AddLine(LOG_CACHE, "miss k: \t" . var_export($k, true));
        } else {
                                            Log::AddLine(LOG_CACHE, "hit k: \t" . var_export($k, true));
                    }
                }
            } else {
                if (!isset($result) || $result === false) {
                                    Log::AddLine(LOG_CACHE, "miss: \t" . var_export($key, true));
                } else {
                                    Log::AddLine(LOG_CACHE, "hit: \t" . var_export($key, true));
                }
            }


        return $result;
    }

    /**
     * @param $key
     * @return bool
     */
    public static function ClearKey($key)
    {
        if (CACHE_ENABLED == 'no') {
            return false;
        }

        $cache = Cache::Create();
        return $cache->_clear_key($key);
    }

    /**
     * @param $key
     * @param $value
     * @param int $lifetime
     * @return bool
     */
    public static function SetKey($key, $value, $lifetime = CACHE_LIFETIME_STANDARD)
    {
        if (CACHE_ENABLED == 'no') {
            return false;
        }

        $cache = Cache::Create();
        return $cache->_set_key($key, $value, $lifetime);
    }

    /**
     * @param $key
     * @return bool|null
     */
    public static function GetKey($key)
    {
        if (CACHE_ENABLED == 'no') {
            return null;
        }

        $cache = Cache::Create();
        return $cache->_get_key($key);
    }

    /**
     * @param $key
     * @return bool
     */
    public static function ClearLock($key)
    {
        if (CACHE_ENABLED == 'no') {
            return false;
        }

        $cache = Cache::Create();
        return $cache->_clear_key(CACHE_LOCK_PREFIX . $key);
    }

    /**
     * @param string $tag
     * @return bool
     */
    public static function ClearTag($tag)
    {
        if (CACHE_ENABLED == 'no') {
            return false;
        }

        $cache = Cache::Create();
        $cache->_clear_key($tag);
        return $cache->_clear_key(CACHE_TAG_PREFIX . $tag);
    }

    /**
     * @param $key
     * @return bool
     */
    function _clear_key($key)
    {
        if (CACHE_PREFIX !== '') {
            $key = CACHE_PREFIX . $key;
        }

        if (empty($this->connection)) {
            return false;
        }

        if (CACHE_LOG == 'yes') {
            Log::AddLine(LOG_CACHE, "clear: \t" . $key);
        }

        return $this->connection->delete($key);
    }

    /**
     * @return bool
     */
    public static function Flush()
    {
        $cache = Cache::Create();
        return $cache->_flush();
    }

    /**
     * @return bool
     */
    function _flush()
    {
        if (empty($this->connection)) {
            return false;
        }

        return $this->connection->flush();
    }

    /**
     * @param $key
     * @param $vall
     */
    public static function appendData($key, $vall)
    {
        $cache = Cache::Create();
        $cache->_appandData($key, $vall);
    }

    /**
     * @param $key
     * @param $vall
     */
    function _appandData($key, $vall)
    {
        $this->connection->set('foo', array('1' => 'vall1', '2' => 'vall2'));
        $this->connection->replace('foo', array('3' => 'vall3'));
        var_dump($this->connection->get('foo'));
    }

}