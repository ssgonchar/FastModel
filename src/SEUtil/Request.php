<?php
/**
 * Created by PhpStorm.
 * User: ssgonchar
 * Date: 17.01.2016
 * Time: 23:55
 */

namespace SSGonchar\FastModel\SEUtil;

/**
 * Class Request
 * @package SSGonchar\FastModel\SEUtil
 */
class Request
{
    /**
     *
     *
     * @return string
     */
    public static function GetVisitorIdInfo()
    {
        $result = array();

        if (array_key_exists('HTTP_USER_AGENT', $_SERVER)) $result['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];

        if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) $result['REMOTE_ADDR'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (array_key_exists('REMOTE_ADDR', $_SERVER)) $result['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'];

        if (isset($_COOKIE['__' . CACHE_PREFIX])) $result['PREV_LOGIN'] = $_COOKIE['__' . CACHE_PREFIX];

        return $result;
    }

    /**
     *
     *
     * @return bool true,
     */
    public static function IsAjax()
    {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']))
            if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
                return true;

        if (isset($_REQUEST['JsHttpRequest']))
            return true;

        return false;
    }

    /**
     *
     *
     * @return bool true,
     */
    public static function IsIE()
    {
        $pos = strpos($_SERVER['HTTP_USER_AGENT'], "MSIE");

        return !($pos === false);
    }

    /**
     *
     *
     * @return bool true,
     */
    public static function IsRss()
    {
        $is_rss = (isset($_REQUEST['is_rss']) and $_REQUEST['is_rss'] == 'yes') ? true : false;

        return $is_rss;
    }

    /**
     *
     *
     * @return bool true,
     */
    public static function IsPrint()
    {
        $is_print = (isset($_REQUEST['is_print']) and $_REQUEST['is_print'] == 'yes') ? true : false;

        return $is_print;
    }

    /**
     *
     *
     * @return string
     */
    public static function InfoForLog()
    {
        $result =
            $_SERVER['REMOTE_ADDR'] . ':' . $_SERVER['SERVER_PORT'] . ' ' .
            $_SERVER['REQUEST_METHOD'] . (Request::IsAjax() ? '(AJAX)' : '') . ' ' .
            (isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : $_SERVER["PHP_SELF"]) . '?' . $_SERVER['QUERY_STRING'];

        return $result;
    }

    /**
     *
     *
     * @param string $name
     * @param array $params
     * @param numeric $default
     * @return numeric
     */
    static function _get_numeric_param($name, $params, $default)
    {
        if (!array_key_exists($name, $params)) return $default;
        if ($params[$name] == '') return $default;

        $value = trim($params[$name]);
        $dig = (substr($value, 0, 1) == '-' ? -1. : 1.);

        $result = preg_replace('/[^0-9,\.]/', '', $params[$name]);

        if ($result == '') return $default;

        $parts = preg_split('/(,|\.)/', $result);
        $resnum = 0;

        if (count($parts) == 1 && !empty($parts[0])) {
            $resnum = $parts[0];
        } else if (count($parts) == 2) {
            if (!empty($parts[0])) $resnum = floatval($parts[0]);
            if (!empty($parts[1])) $resnum += 1. * floatval($parts[1]) / pow(10, strlen($parts[1]));
        }

        return $resnum * $dig;
    }

    /**
     *
     *
     * @param string $name
     * @param array $params
     * @param numeric $default_arg
     * @return numeric
     */
    static function GetNumeric($name, $params)
    {
        $default_arg = func_num_args() > 2 ? func_get_arg(2) : 0.;
        $default = is_numeric($default_arg) ? floatval($default_arg) : 0.;

        return Request::_get_numeric_param($name, $params, $default);
    }

    /**
     *
     *
     * @param string $name
     * @param array $params
     * @param integer $default
     * @return integer
     */
    static function _get_integer_param($name, $params, $default)
    {
        $result = $default;

        if (isset($params[$name]))
            if (is_numeric($params[$name]))
                $result = intval($params[$name]);

        return ($result > 1000000000 ? 1000000000 : $result);
    }

    /**
     *
     *
     * @param string $name
     * @param array $params
     * @return int
     * @internal param int $default_arg
     */
    static function GetInteger($name, $params)
    {
        $default_arg = func_num_args() > 2 ? func_get_arg(2) : 0;
        $default = is_numeric($default_arg) ? intval($default_arg) : 0;

        return Request::_get_integer_param($name, $params, $default);
    }

    /**
     *
     *
     * @param string $name
     * @param array $params
     * @param bool $default
     * @return bool
     */
    static function _get_boolean_param($name, $params, $default)
    {
        $result = $default;

        if (isset($params[$name]))
            if (getboolval($params[$name]))
                $result = true;

        return $result;
    }

    /**
     *
     *
     * @param string $name
     * @param array $params
     * @param bool $default_arg
     * @return bool
     */
    public static function GetBoolean($name, $params)
    {
        $default_arg = func_num_args() > 2 ? func_get_arg(2) : false;
        $default = $default_arg ? true : false;

        return Request::_get_boolean_param($name, $params, $default);
    }

    /**
     *
     *
     * @param string $name
     * @param array $params
     * @param bool $default
     * @param integer $length
     * @param bool $strip_slashes
     * @param bool $strip_tags
     * @return string
     */
    static function _get_string_param($name, $params, $default, $length, $strip_slashes, $strip_tags, $url_go = false)
    {
        $result = isset($default) ? $default : '';

        if (!isset($params)) {
            return $result;
        }

        if (!array_key_exists($name, $params)) {
            return $result;
        }

        $result = trim($params[$name]);

        if ($length > 0) {
            $result = mb_substr($result, 0, $length, 'UTF-8');
        }

        $result = self::_parse_external_links($result, $url_go);

        if ($strip_slashes)
            $result = stripslashes($result);

        if ($strip_tags)
            $result = strip_tags($result);

        return $result;
    }

    /**
     *
     *
     * @param string $name
     * @param array $params
     * @return string
     * @internal param bool $default
     * @internal param int $length
     * @internal param bool $strip_tags
     */
    static function GetString($name, $params)
    {
        $default = func_num_args() > 2 ? func_get_arg(2) : '';
        $length = func_num_args() > 3 ? func_get_arg(3) : null;
        $strip_tags = func_num_args() > 4 ? func_get_arg(4) : false;
        $strip_slashes = get_magic_quotes_gpc() ? true : false;

        return self::_get_string_param($name, $params, $default, $length, $strip_slashes, $strip_tags);
    }

    /**
     *
     *
     * @param string $name
     * @param array $params
     * @param bool $default
     * @param integer $length
     * @param bool $strip_tags
     * @return string
     */
    static function GetHtmlString($name, $params, $url_go = false)
    {
        $default = func_num_args() > 2 ? func_get_arg(2) : '';
        $length = func_num_args() > 3 ? func_get_arg(3) : null;
        $strip_tags = func_num_args() > 4 ? func_get_arg(4) : false;
        $strip_slashes = get_magic_quotes_gpc() ? true : false;

        $text = self::_get_string_param($name, $params, $default, $length, $strip_slashes, $strip_tags, $url_go);

        //
        $text = self::_filter_tags($text);

        //
        preg_match_all('#<iframe[^<]*youtube\.com\/embed\/([a-zA-Z0-9_]+)[^<]*><\/iframe>#si', $text, $youtubes);
        foreach ($youtubes[0] as $key => $match) $text = str_replace($match, '{' . $youtubes[1][$key] . '}', $text);

        //
        $text = self::_purify_html($text, $auto_paragraph = false, $allow_youtube = false, $url_go);

        //
        foreach ($youtubes[0] as $key => $match) $text = str_replace('{' . $youtubes[1][$key] . '}', $match, $text);

        return $text;
    }

    /**
     *
     *
     * @param mixed $text
     * @return mixed
     */
    static function _filter_tags($text)
    {
        $entries = array();
        $allow_entries = array('<a>', '<area>', '<b>', '<big>', '<blockquote>', '<br>', '<caption>', '<center>', '<dd>', '<div>',
            '<dl>', '<dt>', '<em>', '<font>', '<h1>', '<h2>', '<h3>', '<h4>', '<h5>', '<h6>', '<hr>', '<i>', '<img>',
            '<li>', '<map>', '<object>', '<ol>', '<p>', '<pre>', '<small>', '<span>', '<strike>', '<strong>',
            '<style>', '<sub>', '<sup>', '<table>', '<tbody>', '<td>', '<tfoot>', '<th>', '<thead>', '<tr>', '<u>',
            '<ul>', '<iframe>');

        preg_match_all('|<(.+)>|U', $text, $matches);

        $matches = $matches[1];
        for ($i = 0; $i < count($matches); $i++) {
            $parts = explode(' ', $matches[$i]);

            if (!array_key_exists($matches[$i], $entries)) {
                $entries[trim($matches[$i])] = str_replace('/', '', $parts[0]);
            }
        }

        //
        foreach ($entries as $key => $value) {
            if (array_search('<' . $value . '>', $allow_entries) === false) {
                $text = str_replace('<' . $key . '>', '', $text);
            }
        }

        return $text;
    }

    /**
     *
     *
     * @param mixed $text
     * @param mixed $auto_paragraph
     * @param mixed $allow_youtube
     * @return mixed
     */
    static function _purify_html($text, $auto_paragraph = false, $allow_youtube = false, $url_go = false)
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('Cache.SerializerPath', APP_CACHE);

        $config->set('URI.Host', APP_HOST);

        if ($url_go) {
            $config->set('URI.Munge', '%s');
        } else {
            $config->set('URI.Munge', APP_HOST . '/go/%s');
        }

        $config->set('Core.Encoding', 'UTF-8');
        $config->set('HTML.Doctype', 'XHTML 1.0 Strict');
        $config->set('AutoFormat.AutoParagraph', $auto_paragraph);
        $config->set('AutoFormat.PurifierLinkify', true);
        $config->set('AutoFormat.Linkify', true);
        $config->set('Output.Newline', "\n");

        $config->set('Core.MaintainLineNumbers', true);
        $config->set('Core.CollectErrors', true);


        if (isset($_SESSION['user']) && ($_SESSION['user']['role_id'] > 0 && $_SESSION['user']['role_id'] <= ROLE_MODERATOR)) {
            $config->set('Attr.EnableID', true);
        }

        if ($allow_youtube) {
            $config->set('Filter.YouTube', true);
        }

        //
        //$config->set('Core', 'EscapeInvalidTags', true);

        $purifier = new HTMLPurifier($config);

        $result = $text;

        if (!$url_go) {
            $result = $purifier->purify($result);
            $e = $purifier->context->get('ErrorCollector');
            Log::AddLine(LOG_CUSTOM, "Request::GetHtmlString() errors=" . $e->getHTMLFormatted($config));
        }

        $result = preg_replace('/\<\/p\>(\\n|\\r)+\</u', '</p><', $result);

        Log::AddLine(LOG_CUSTOM, "Request::GetHtmlString() result=" . $result);

        return $result;
    }

    /**
     *
     *
     * @param string $str
     */
    function GetStringArray($name, $param)
    {
        $escaped_str = Request::GetString($name, $param);

        $result = str_replace(' ', '', $escaped_str);
        //debug('1682', $result);
        $array_thickness = explode(',', $result);
        return $array_thickness;
    }

    /**
     *
     *
     * @param array $files
     * @param string $custom
     * @param string $custom,...
     * @return array
     */
    static function GetFile($files)
    {
        $fields = array('name', 'type', 'size', 'tmp_name');
        $result = array();

        $custom = array();
        for ($i = 1; $i < func_num_args(); $i++)
            $custom[] = func_get_arg($i);

        foreach ($fields as $key) {
            $current = $files[$key];
            for ($i = 0; $i < count($custom); $i++) {
                if (isset($current[$custom[$i]]))
                    $current = $current[$custom[$i]];
                else
                    break;
            }

            $result[$key] = $current;
        }

        if (count($result) > 0) ;
        if ($result['size'] > 0)
            return $result;

        return null;
    }


    /**
     *
     * @name =
     * @value =
     * @default =
     */
    static function GetStringDate($name, $value, $default = null, $include_time = false)
    {
        if (!array_key_exists($name, $value)) return null;

        if (($timestamp = strtotime($value[$name])) === -1) {
            return $default == null ? now() : $default;
        } else {
            return $include_time == false ? date('Y-m-d 00:00:00', $timestamp) : date('Y-m-d h:i:s', $timestamp);
        }
    }

    /**
     *
     *
     * @param string $param
     * @param integer $Day
     * @param integer $Month
     * @param integer $Year
     * @return string
     */
    static function GetDate($param, $params)
    {
        $Day = Request::GetInteger($param . 'Day', $params);
        $Month = Request::GetInteger($param . 'Month', $params);
        $Year = Request::GetInteger($param . 'Year', $params);

        if (!checkdate($Month, $Day, $Year)) {
            if (func_num_args() > 3) {
                $Day = func_get_arg(1);
                $Month = func_get_arg(2);
                $Year = func_get_arg(3);
            } else {
                $Day = date('d');
                $Month = date('m');
                $Year = date('Y');
            }
        }

        return sprintf('%4d-%02d-%02d', $Year, $Month, $Day);
    }

    /**
     *
     *
     *
     *
     * @param string $param
     * @param integer $Day
     * @param integer $Month
     * @param integer $Year
     * @return string
     */
    static function GetJaggedDate($param)
    {
        $Day = 0;
        $Month = 0;
        $Year = 0;

        if (func_num_args() > 3) {
            $Day = func_get_arg(1);
            $Month = func_get_arg(2);
            $Year = func_get_arg(3);
        }

        $Day = Request::GetInteger($param . 'Day', $Day);
        $Month = Request::GetInteger($param . 'Month', $Month);
        $Year = Request::GetInteger($param . 'Year', $Year);

        if ($Year == 0)
            $Month = 0;
        if ($Month == 0)
            $Day = 0;

        if (!checkdate(($Month > 0 ? $Month : 1), ($Day > 0 ? $Day : 1), ($Year > 0 ? $Year : 1))) {
            $Day = date('d');
            $Month = date('m');
            $Year = date('Y');
        }

        return sprintf('%04d%02d%02d', $Year, $Month, $Day);
    }

    /**
     *
     *
     * @param string $param
     * @return string
     */
    static function GetDateTime($param, $params)
    {
        $default = func_num_args() > 2 ? func_get_arg(2) : null;
        $Day = Request::GetInteger($param . 'Day', $params);
        $Month = Request::GetInteger($param . 'Month', $params);
        $Year = Request::GetInteger($param . 'Year', $params);

        if (empty($Day) || empty($Month) || empty($Year)) return $default;

        if (checkdate($Month, $Day, $Year))
            $date = mktime(0, 0, 0, $Month, $Day, $Year);
        else
            $date = time();

        return date('Y-m-d H:i:s', $date);
    }

    /**
     *
     *
     *
     * @param string $param
     * @param array $value
     * @return string
     * /
     */
    static function GetDateForDB($name, $params)
    {
        $date = Request::GetString($name, $params);

        $date = explode('/', $date);

        settype($date[0], 'integer');
        settype($date[1], 'integer');
        settype($date[2], 'integer');

        if (checkdate($date[1], $date[0], $date[2])) {
            if ($date[2] < 1900) {
                $date = null;
            } else {
                $date = mktime(0, 0, 0, $date[1], $date[0], $date[2]);
                $date = date('Y-m-d H:i:s', $date);
            }
        } else {
            $date = null;
        }

        return $date;
    }

    /**
     *
     *
     *
     * @param mixed $text
     */
    static function _parse_external_links($text, $url_go = false)
    {

        preg_match_all("/<img[^<]*>/si", $text, $images);
        if (!empty($images)) {
            $images = $images[0];
            foreach ($images as $image) {
                preg_match("/src=\"([^\"']*)\"/si", $image, $url);
                if (!empty($url)) {
                    //
                    if (strpos($url[1], 'data') === 0) {
                        $text = str_replace($image, '', $text);
                        continue;
                    }

                    //style="height: 188px; width: 250px;"
                    preg_match("/style=\"([^\"']*)\"/si", $image, $style);
                    if (!empty($style)) {
                        $style = $style['1'];

                        preg_match("/height:(.+?)px;/si", $style, $height);
                        if (!empty($height)) {
                            //$height = $height[1] > 500 ? 500 : $height[1];
                            $height = $height[1];
                        }

                        preg_match("/width:(.+?)px;/si", $style, $width);
                        if (!empty($width)) {
                            //$width = $width > 500 ? '' : $width;
                            $width = $width[1];
                        }

                        if ($width > $height) {
                            $height = null;
                        } else
                            $width = null;

                        if (!empty($width)) {
                            $width = $width > 500 ? 500 : $width;
                        }

                        if (!empty($height)) {
                            $height = $height > 500 ? 500 : $height;
                        }

                        $replacement = '<img style="' . (!empty($width) ? 'width:' . $width . 'px;' : "") . (!empty($height) ? 'height:' . $height . 'px;' : "") . '"' . ' src="' . $url[1] . '">';
                    } else {
                        preg_match("/height=\"([^\"']*)\"/si", $image, $height);
                        if (!empty($height)) {
                            $height = $height[1] > 500 ? 500 : $height[1];
                        }

                        preg_match("/width=\"([^\"']*)\"/si", $image, $width);
                        if (!empty($width)) {
                            $width = $width[1] > 500 ? '' : $width[1];
                        }

                        $replacement = '<img src="' . $url[1] . '"' . (!empty($height) ? ' height="' . $height . 'px"' : '') . (!empty($width) ? ' width="' . $width . 'px"' : '') . '>';
                    }

                    $text = str_replace($image, $replacement, $text);
                } else {
                    $text = str_replace($image, '', $text);
                }
            }
        }
    }
}