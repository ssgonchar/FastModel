<?php
/**
     * Created by PhpStorm.
     * User: ssgonchar
     * Date: 18.01.2016
     * Time: 2:37
     */

namespace SSGonchar\FastModel\SEUtil;

define('LOG_REQUEST', 1);
define('LOG_ERROR', 2);
define('LOG_QUERY', 3);
define('LOG_CUSTOM', 4);
define('LOG_CACHE', 5);
define('LOG_SLOW_QUERIES', 6);
define('LOG_APP_WARNING', 7);
define('LOG_EMAIL_GRABBER', 8);

/**
 * Class Log
 * @package SSGonchar\FastModel\SEUtil
 */
class Log
{
    /**
     *
     *
     * @var string
     */
    var $log_file;

    /**
     *
     *
     * @var integer
     */
    var $fp;

    /**
     * @param string $log_file
     */
    function __construct($log_file)
    {
        if (LOG != 'yes') {
            return;
        }

        $this->log_file = $log_file;
        $this->fp = fopen($log_file, 'a');

        if (!$this->fp) {
            chmod($log_file, 0777);
            $this->fp = fopen($log_file, 'a');
        }
    }

    /**
     * @param integer $type
     * @param string $log_file
     * @return Log
     */
    public static function Create($type, $log_file)
    {
        if ($type == LOG_EMAIL_GRABBER) {
            $key = 4;
        } else if ($type == LOG_SLOW_QUERIES) {
            $key = 3;
        } else if ($type == LOG_CACHE) {
            $key = 2;
        } else {
            $key = 1;
        }

        static $instance;
        if (!isset($instance)) {
            $instance = array($key => new Log($log_file));
        } else if (!array_key_exists($key, $instance)) {
            $instance[$key] = new Log($log_file);
        }

        return $instance[$key];
    }

    /**
     *
     */
    function Destructor()
    {
        fclose($this->fp);
    }

    /**
     * @return string
     */
    function _time()
    {
        return date('Y-m-d H:i:s ');
    }

    /**
     * @param $type
     * @param $str
     * @return string
     */
    function _formatLine($type, $str)
    {
        if ($type == LOG_ERROR || $type == LOG_APP_WARNING || $type == LOG_EMAIL_GRABBER) {
            $result = '----------------------------------------------------------------------------------------------------------';
            $result .= "\n" . $this->_time() . ' ERROR';
            $result .= "\n" . $str . "\n";
            $result .= '----------------------------------------------------------------------------------------------------------';
            $result .= "\n\n";
            return $result;
        } else {
            $result = $this->_time();

            switch ($type) {
                case LOG_REQUEST:
                    $result .= ' REQUEST =============================================================================';
                    break;

                case LOG_QUERY:
                    $result .= ' QUERY';
                    break;
            }

            return $result . "\n" . $str . "\n\n";
        }
    }

    /**
     * @param $str
     */
    public static function Bugtruck($str)
    {

        $type = LOG_ERROR;
        $filename = APP_LOGS . date('Ymd') . '.addmsg.txt';
        //die($filename);
        $log = Log::Create($type, $filename);
        $log->_addLine($type, $str);
    }

    /**
     * @param integer $type
     * @param $str
     */
    public static function AddLine($type, $str)
    {
        if (LOG != 'yes') {
            return;
        }

        if ($type == LOG_CACHE) {
            $filename = APP_LOGS . date('Ymd') . '.cache.txt';
        } else if ($type == LOG_SLOW_QUERIES) {
            $filename = APP_LOGS . date('Ymd') . '.slow.txt';
        } else if ($type == LOG_EMAIL_GRABBER) {
            $filename = APP_LOGS . date('Ymd') . '.emailgrabber.txt';
        } else {
            $filename = APP_LOGS . date('Ymd') . '.app.txt';
        }

        $log = Log::Create($type, $filename);
        $log->_addLine($type, $str);
    }

    /**
     * @param integer $type
     * @param $str
     */
    function _addLine($type, $str)
    {
        if (fwrite($this->fp, $this->_formatLine($type, $str)) == -1) {
            die('Error writing to log');
        }
    }

    public static function DeleteDirectory($dir)
    {
        if (!file_exists($dir)) {
            return true;
        }
        if (!is_dir($dir) || is_link($dir)) {
            return unlink($dir);
        }
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            if (!self::deleteDirectory($dir . "/" . $item)) {
                chmod($dir . "/" . $item, 0777);
                if (!self::deleteDirectory($dir . "/" . $item)) {
                    return false;
                }
            };
        }
        return rmdir($dir);
    }
}