<?php
/**
 * Created by PhpStorm.
 * User: ssgonchar
 * Date: 14.01.2016
 * Time: 1:26
 */

namespace SSGonchar\FastModel\SEModel;

use SSGonchar\FastModel\SEUtil\Cache;
use SSGonchar\FastModel\SEUtil\Db\Table;
use SSGonchar\FastModel\SEUtil\Request;

//use SSGonchar\FastModel\SEUtil\Request;

/**
 * Class Model
 * @package SSGonchar\FastModel\SEModel
 */
class Model
{
    /**
     * @var mixed
     */
    public $params;

    /**
     * @var Table
     */
    public $table;

    /**
     * @var int
     */
    public $user_id = 0;

    /**
     * @var int|string
     */
    public $user_role = ROLE_GUEST;

    /**
     * @var string
     */
    public $user_login = '';

    /**
     * @var string
     */
    public $session_id = '';

    /**
     * @var array
     */
    public $messages;

    /**
     * @var
     */
    public $object_alias;

    /**
     * @var string
     */
    public $lang = DEFAULT_LANG;

    /**
     * @var array
     */
    public static $session;

    /**
     * @param string $tableName
     */
    public function __construct($tableName)
    {
        /**
         * Set default connection settings.
         */
        $defaultConnectionSettings = array(
            'dbhost' => APP_DBHOST,
            'dbname' => APP_DBNAME,
            'dbuser' => APP_DBUSER,
            'dbpass' => APP_DBPASS,
            'charset' => 'utf8_general_ci'
        );

        /**
         * Set custom connection settings if passed second arg in constructor,
         * else using default settings.
         */
        $connectionSettings = func_num_args() > 1 ? func_get_arg(1) : $defaultConnectionSettings;

        /**
         * Custom connection settings must be array,
         * else use default settings.
         */
        if (!is_array($connectionSettings)) {
            $connectionSettings = $defaultConnectionSettings;
        }

        /**
         * Create instance of DB connection.
         */
        $this->table = new Table($tableName, $connectionSettings);

        $this->messages = array();

        /**
         * Set user current user information.
         */
        $session = self::getSession();
        if (array_key_exists('user', $session)) {
            $this->user_id = $session['user']['id'];
            $this->user_login = $session['user']['login'];
            $this->user_role = $session['user']['role_id'];
        }

        /**
         * Set user session id.
         */
        $this->session_id = session_id();

        /**
         * Set current session language.
         */
        $this->lang = isset($_REQUEST['lang']) ? Request::GetString('lang', $_REQUEST, '', 2) : $this->lang;
    }

    /**
     * @param $session
     */
    public static function setSession($session = null)
    {
        self::$session = $session;
    }

    /**
     * @return array|void
     */
    public static function getSession()
    {
        if (!self::$session) {
            self::setSession($_SESSION);
        }
        return self::$session;
    }

    /**
     * This callback will run before SELECT query.
     */
    public function _before_select()
    {

    }

    /**
     * This method can be used for setting required conditions for query.
     * @return bool
     */
    public function _validate_select()
    {
        return true;
    }

    /**
     *
     */
    public function _on_select()
    {

    }

    /**
     * This callback will run before INSERT query.
     */
    public function _before_insert()
    {

    }

    /**
     * This method can be used for setting required conditions for query.
     * @param $values
     * @return bool
     */
    public function _validate_insert($values)
    {
        return true;
    }

    /**
     *
     */
    public function _on_insert()
    {

    }

    /**
     * This callback will run before INSERT query for list.
     */
    public function _before_insert_list()
    {

    }

    /**
     * This method can be used for setting required conditions for query.
     * @param $fields
     * @param $values
     * @return bool
     */
    public function _validate_insert_list($fields, $values)
    {
        return true;
    }

    /**
     *
     */
    public function _on_insert_list()
    {

    }

    /**
     * This callback will run before DELETE query.
     */
    public function _before_delete()
    {

    }


    /**
     * This method can be used for setting required conditions for query.
     * @return bool
     */
    public function _validate_delete()
    {
        return true;
    }


    /**
     *
     */
    public function _on_delete()
    {
    }


    /**
     * This callback will run before UPDATE query
     */
    public function _before_update()
    {
    }


    /**
     * This method can be used for setting required conditions for query.
     * @param $values
     * @return bool
     */
    public function _validate_update($values)
    {
        return true;
    }

    /**
     *
     */
    public function _on_update()
    {
    }

    /**
     * This method can be used for setting required conditions for query.
     * @return bool
     */
    public function _validate_update_list()
    {
        return true;
    }

    /**
     * Add message.
     * @param $message
     * @param $status
     */
    public function _add_message($message, $status)
    {
        if (isset($message)) {
            if ($message != '') {
                $this->messages[] = array('text' => $message, 'status' => $status);
            }
        }
    }

    /**
     * Select list of rows.
     * @param array $arg
     * @return array
     */
    public function SelectList($arg = array())
    {
        /**
         * Set query parameters.
         */
        if (is_string($arg)) {
            $this->params = array('where' => array('conditions' => $arg));
        } else if (!is_array($arg)) {
            $this->params = array();
        } else {
            $this->params = $arg;
        }

        if ($this->_validate_select()) {
            $this->_before_select();

            $result = $this->table->SelectList($this->params);

            $this->_on_select();
        }

        return $result;
    }

    /**
     * Select single row by id.
     * @param int $id
     * @return array
     */
    public function Select($id)
    {
        $this->params = array('where' => array('conditions' => 'id=?', 'arguments' => $id));

        if ($this->_validate_select()) {
            $this->_before_select();

            $result = $this->table->Select($id);

            $this->_on_select();
        }

        return $result;
    }

    /**
     * Select single row using parameters from $arg.
     * @param array $arg
     * @return array
     */
    public function SelectSingle($arg = array())
    {
        $this->params = $arg;

        $result = $this->table->SelectSingle($this->params);

        return $result;
    }

    /**
     * Get number of rows which consists parameters from $arg.
     * @param array $arg
     * @return int
     */
    public function Count($arg = array())
    {
        $this->params = $arg;

        return $this->table->Count($this->params);
    }

    /**
     * @see DatabaseConnection::FoundRows()
     * @return int
     */
    public function FoundRows()
    {
        return $this->table->FoundRows();
    }

    /**
     * Delete list of rows which consists parameters from $arg.
     * @param array $arg
     * @return int
     */
    public function DeleteList($arg = array())
    {
        $this->params = $arg;

        $result = 0;

        if ($this->_validate_select()) {
            $this->_before_delete();

            $result = $this->table->DeleteList($this->params);

            $this->_on_delete();
        }

        return $result;
    }

    /**
     * Delete single row with id $id.
     * @param $id
     * @return int
     */
    public function DeleteSingle($id)
    {
        $this->params = array('where' => array('conditions' => 'id=?', 'arguments' => $id));

        $result = 0;

        if ($this->_validate_delete()) {
            $this->_before_delete();

            $result = $this->table->Delete($id);

            $this->_on_delete();
        }

        return $result;
    }

    /**
     * Check exists rows which parameters from $arg.
     * @param array $arg
     * @return bool
     */
    public function Exists($arg = array())
    {
        $this->params = $arg;

        return $this->table->Exists($this->params);
    }

    /**
     * Insert new row with values from $values.
     * If $ignore_unique_error will be inserted only unique rows. @see Table::Insert()
     * @param $values
     * @param bool|false $ignore_unique_error
     * @return int
     */
    public function Insert($values, $ignore_unique_error = false)
    {
        if ($this->_validate_insert($values)) {
            $this->_before_insert();

            $result = $this->table->Insert($values, $ignore_unique_error);

            $this->_on_insert();
        } else {
            $result = 0;
        }

        return $result;
    }

    /**
     * Insert new rows list with values from $values.
     * @param $fields
     * @param $values
     * @param bool|false $ignore_unique_error
     */
    public function InsertList($fields, $values, $ignore_unique_error = false)
    {
        if ($this->_validate_insert_list($fields, $values)) {
            $this->_before_insert();

            $this->table->InsertList($fields, $values, $ignore_unique_error);

            $this->_on_insert();
        }
    }

    /**
     * Replace duplicated row.
     * @param $values
     * @return int
     */
    public function Replace($values)
    {
        if ($this->_validate_insert($values)) {
            $this->_before_insert();

            $result = $this->table->Replace($values);

            $this->_on_insert();
        } else {
            $result = 0;
        }

        return $result;
    }

    /**
     * Replace duplicated rows list.
     * @param $fields
     * @param $values
     */
    public function ReplaceList($fields, $values)
    {
        if ($this->_validate_insert_list($fields, $values)) {
            $this->_before_insert();

            $this->table->ReplaceList($fields, $values);

            $this->_on_insert();
        }
    }

    /**
     * Update row.
     * @param $id
     * @param $values
     * @param bool|false $ignore_unique_error
     * @return int
     */
    public function Update($id, $values, $ignore_unique_error = false)
    {
        $this->params = array('values' => $values, 'where' => array('conditions' => 'id=?', 'arguments' => $id));
        if ($this->_validate_update($values)) {
            $this->_before_update();

            $this->table->Update($id, $values, $ignore_unique_error);

            $this->_on_update();
        } else {
            $id = 0;
        }

        return $id;
    }

    /**
     * Update rows list.
     * @param $params
     * @param bool|false $ignore_unique_error
     * @return int
     */
    function UpdateList($params, $ignore_unique_error = false)
    {
        if ($this->_validate_update_list()) {
            $this->_before_update();

            $result = $this->table->UpdateList($params, $ignore_unique_error);
            $this->_on_update();
        }

        return $result;
    }

    /**
     * Execute raw SQL query.
     * @param $sql_text
     * @return resource|null
     */
    public function ExecuteQuery($sql_text)
    {
        if (trim($sql_text) != '') {
            return $this->table->db->query($sql_text);
        }
    }

    /**
     * Call SQL stored procedure.
     * @param $name
     * @param $values
     * @return resource
     */
    public function CallStoredProcedure($name, $values)
    {
        //require_once(APP_PATH . 'classes/models/sp.class.php');

        ///print_r($name);
        //print_r($values);

        $sp = new Sp();
        //print_r('c');
        if (method_exists($sp, $name)) {
            //print_r('a');
            return $sp->$name($values);
        } else {
            //print_r('b');
            return $this->table->CallStoredProcedure($name, $values);
        }
    }

    /**
     * Call SQL stored function.
     * @param $name
     * @param $values
     * @return null
     */
    public function CallStoredFunction($name, $values)
    {
        return $this->table->CallStoredFunction($name, $values);
    }

    /**
     * Clear records in cache with tag used $id.
     * @param $id
     */
    public function ClearTagById($id)
    {
        if (!empty($this->object_alias)) {
            Cache::ClearTag($this->object_alias . '-' . $id);
        }
    }

    /**
     * Set DB connection time zone.
     * @param $time_zone
     */
    public function SetConnectionTimeZone($time_zone)
    {
        if (!empty($time_zone)) {
            $this->table->connection_time_zone = $time_zone;
        }
    }

    /**
     * Extract entities.
     * @param $data
     * @return mixed
     */
    public function _extract_entities($data)
    {
        for ($i = 0; $i < count($data); $i++) {
            foreach ($data[$i] as $key => $value) {
                $pos1 = strpos($key, '_');

                if ($pos1 !== false && $pos1 === 0) {
                    $pos2 = strpos($key, '_', 1);

                    if ($pos2 !== false) {
                        $entity = substr($key, 1, $pos2 - 1);
                        $newkey = substr($key, $pos2 + 1);
                        if (!array_key_exists($entity, $data[$i])) {
                            $data[$i][$entity] = array();
                        }
                        $data[$i][$entity][$newkey] = $value;
                        unset($data[$i][$key]);
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Get data from cache.
     * @param $hash
     * @param $sp_name
     * @param $sp_params
     * @param array $cache_tags
     * @param $lifetime
     * @return null|resource
     */
    public function _get_cached_data($hash, $sp_name, $sp_params, $cache_tags = array(), $lifetime = CACHE_LIFETIME_STANDARD)
    {
        $rowset = Cache::GetData($hash);

        if (isset($rowset) && isset($rowset['data']) && !isset($rowset['outdated'])) {
            return $rowset['data'];
        }

        $iamlocker = Cache::SetLock($hash);

        if ($iamlocker) {
            $rowset = $this->CallStoredProcedure($sp_name, $sp_params);
            Cache::SetData($hash, $rowset, $cache_tags, $lifetime);
            Cache::ClearLock($hash);
            return $rowset;
        }

        $has_old_data = isset($rowset['data']);
        $counter = 0;
        do {
            $counter++;

            if ($counter == 4 && $has_old_data) {
                return $rowset['data'];
            }

            if ($counter == 16) {
                return null;
            }

            sleep(1);

        } while ($locked = (Cache::IsLocked($hash)));

        if (!$locked) {
            $rowset = Cache::GetData($hash);
            return isset($rowset) && isset($rowset['data']) ? $rowset['data'] : null;
        }

        return null;
    }

    /**
     * Fill entities in array with information.
     * @param $recordset
     * @param $id_fieldname
     * @param $entityname
     * @param $cache_prefix
     * @param $sp_name
     * @param null $tags
     * @param null $sp_params
     * @return array
     */
    public function _fill_entity_array_info($recordset, $id_fieldname, $entityname, $cache_prefix, $sp_name, $tags = null, $sp_params = null)
    {
        if (!isset($recordset) || empty($recordset) || !is_array($recordset)) {
            return $recordset;
        }

        $first_key = key($recordset);
        if (!isset($recordset[$first_key]) || !is_array($recordset[$first_key])) {
            return $recordset;
        }

        $entity_ids = array();
        foreach ($recordset as $key => $row) {
            if (isset($recordset[$key][$id_fieldname])) {
                $entity_ids[] = $recordset[$key][$id_fieldname];
            }
        }

        $list = $this->_get_entities_array_by_ids($cache_prefix, $sp_name, $entity_ids, $tags, $sp_params, $id_fieldname);

        foreach ($recordset as $key => $row) {
            if (isset($recordset[$key][$id_fieldname]) && isset($list[$recordset[$key][$id_fieldname]]) && !empty($list[$recordset[$key][$id_fieldname]])) {
                $recordset[$key][$entityname] = $this->_adjust_date($list[$recordset[$key][$id_fieldname]]);
            }
        }

        return $recordset;
    }

    /**
     * Get entities in array by IDs.
     * @param $cache_prefix
     * @param $sp_name
     * @param $ids
     * @param $tags
     * @param null $sp_params
     * @param string $id_fieldname
     * @return array
     */
    public function _get_entities_array_by_ids($cache_prefix, $sp_name, $ids, $tags, $sp_params = null, $id_fieldname = 'id')
    {
        $result = array();
        $ids_not_in_cache = array();

        foreach ($ids as $id) {
            $object = Cache::GetData($cache_prefix . '-' . $id);

            if (!empty($object) && isset($object['data']) && empty($object['outdated'])) {
                $result[$id] = $object['data'];
            } else {
                if (!in_array($id, $ids_not_in_cache) && $id > 0) {
                        $ids_not_in_cache[] = $id;
                }
            }
        }

        $ids_strs = array();

        $hundreds = -1;
        for ($i = 0; $i < count($ids_not_in_cache); $i++) {
            if ($i % 100 == 0) {
                $hundreds++;
                $ids_strs[$hundreds] = '';
            }

            $ids_strs[$hundreds] .= intval($ids_not_in_cache[$i]) . ',';
        }

        foreach ($ids_strs as $ids_str) {
            $params = array();
            $params[] = trim($ids_str, ',');

            if (!empty($sp_params)) {
                if (is_array($sp_params)) {
                    foreach ($sp_params as $param) {
                        array_push($params, $param);
                    }
                } else {
                    array_push($params, $sp_params);
                }
            }

            $rowset = $this->CallStoredProcedure($sp_name, $params);

            if (isset($rowset[0])) {
                foreach ($rowset[0] as $row) {
                    if (isset($row[$id_fieldname])) {
                        $id_fieldname_id = $row[$id_fieldname];

                        if (!isset($result[$id_fieldname_id])) {
                            $result[$id_fieldname_id] = array();
                        }

                        $result[$id_fieldname_id][] = $row;
                    }
                }
            }
        }

        foreach ($result as $id => $row) {
            $cache_id = $cache_prefix . '-' . $id;

            $cachetags = array();
            $cachetags[] = $cache_id;
            if (!empty($tags)) {
                foreach ($tags as $tagname => $fieldname) {
                    if (empty($fieldname)) {
                        $cachetags[] = $tagname;
                    } else if (isset($row[$fieldname])) {
                        $cachetags[] = $tagname . '-' . $row[$fieldname];
                    }
                }
            }

            Cache::SetData($cache_id, $row, $cachetags, CACHE_LIFETIME_STANDARD);
        }

        return $result;
    }

    /**
     * @param $recordset
     * @param $id_fieldname
     * @param $entityname
     * @param $cache_prefix
     * @param $sp_name
     * @param null $tags
     * @param null $sp_params
     * @return array
     */
    public function _fill_entity_info($recordset, $id_fieldname, $entityname, $cache_prefix, $sp_name, $tags = null, $sp_params = null)
    {
        if (!isset($recordset) || empty($recordset) || !is_array($recordset)) {
            return $recordset;
        }

        $first_key = key($recordset);
        if (!isset($recordset[$first_key]) || !is_array($recordset[$first_key])) {
            return $recordset;
        }

        $entity_ids = array();
        foreach ($recordset as $key => $row) {
            if (isset($recordset[$key][$id_fieldname])) {
                $entity_ids[] = $recordset[$key][$id_fieldname];
            }
        }

        $list = $this->_get_entities_by_ids($cache_prefix, $sp_name, $entity_ids, $tags, $sp_params);
        foreach ($recordset as $key => $row) {
            if (isset($recordset[$key][$id_fieldname]) && isset($list[$recordset[$key][$id_fieldname]])) {
                $recordset[$key][$entityname] = $this->_adjust_date($list[$recordset[$key][$id_fieldname]]);
            }
        }

        return $recordset;
    }


    /**
     * @param $cache_prefix
     * @param $sp_name
     * @param $ids
     * @param $tags
     * @param null $sp_params
     * @return array
     */
    public function _get_entities_by_ids($cache_prefix, $sp_name, $ids, $tags, $sp_params = null)
    {
        $result = array();

        $ids_not_in_cache = array();

        foreach ($ids as $id) {

            $object = Cache::GetData($cache_prefix . '-' . $id);
            if (!empty($object) && !empty($object['data']) && empty($object['outdated'])) {
                $result[$id] = $object['data'];
            } else {

                if (!in_array($id, $ids_not_in_cache) && $id > 0) {
                    $ids_not_in_cache[] = $id;
                }
            }
        }

        $ids_strs = array();

        $hundreds = -1;
        for ($i = 0; $i < count($ids_not_in_cache); $i++) {
            if ($i % 100 == 0) {
                $hundreds++;
                $ids_strs[$hundreds] = '';
            }
            $ids_strs[$hundreds] .= intval($ids_not_in_cache[$i]) . ',';
        }

        foreach ($ids_strs as $ids_str) {
            $params = array();
            $params[] = trim($ids_str, ',');

            if (!empty($sp_params)) {
                if (is_array($sp_params)) {
                    foreach ($sp_params as $param) {
                        array_push($params, $param);
                    }
                } else {
                    array_push($params, $sp_params);
                }
            }

            $rowset = $this->CallStoredProcedure($sp_name, $params);

            if (isset($rowset[0])) {
                foreach ($rowset[0] as $row) {
                    if (!isset($row['id'])) {
                        continue;
                    }

                    $cache_id = $cache_prefix . '-' . $row['id'];

                    $cachetags = array();
                    $cachetags[] = $cache_id;
                    if (!empty($tags)) {
                        foreach ($tags as $tagname => $fieldname) {
                            if (empty($fieldname)) {
                                $cachetags[] = $tagname;
                            } else if (isset($row[$fieldname])) {
                                $cachetags[] = $tagname . '-' . $row[$fieldname];
                            }
                        }
                    }

                    $row = $this->_adjust_row($row);
                    Cache::SetData($cache_id, $row, $cachetags, CACHE_LIFETIME_STANDARD);
                    $result[$row['id']] = $row;
                }
            }
        }

        return $result;
    }

    /**
     * @param mixed $row
     * @return array()
     */
    public function _adjust_row($row)
    {
        return $row;
    }

    /**
     * @param array $data
     * @return array
     */
    public function _sort_tree($data)
    {
        if (empty($data)) {
            return $data;
        }

        $result = array();
        $count = count($data);
        $minpid = PHP_INT_MAX;

        for ($i = 0; $i < $count; $i++) {
            if ($data[$i]['id'] < PHP_INT_MAX) {
                $minpid = $data[$i]['parent_id'];
                if ($minpid == 0) {
                    break;
                }
            }
        }

        $pidstack = array($minpid);

        do {
            if (!count($pidstack)) {
                break;
            }

            $parent_id = $pidstack[count($pidstack) - 1];
            $exists = false;

            for ($i = 0; $i < $count; $i++) {
                if (!isset($data[$i]['handled']) && ($data[$i]['id'] == $parent_id || $data[$i]['parent_id'] == $parent_id)) {
                    array_push($pidstack, $data[$i]['id']);
                    $result[] = $data[$i];
                    $data[$i]['handled'] = true;
                    $exists = true;
                    break;
                }
            }

            if (!$exists) {
                array_pop($pidstack);
            }

        } while ($exists || count($pidstack));

        return $result;
    }

    /**
     * @param mixed $title
     * @param mixed $pattern
     * @return string
     */
    public function _get_title_src($title, $pattern = '')
    {
        $pattern = empty($pattern) ? "[^a-zA-Z0-9-]+" : $pattern;

        $title = strtolower(\Translit::Encode($title));
        $title = preg_replace("#" . $pattern . "#us", '', $title);

        preg_match_all("#(\d+-\d+)+#us", $title, $matches);
        if (!empty($matches)) {
            foreach ($matches[0] AS $match) {
                $title = str_replace($match, str_replace('-', '&', $match), $title);
            }
        }

        return md5(str_replace('&', '-', str_replace('-', '', $title)));
    }

    /**
     * @param mixed $row
     * @return mixed
     */
    public function _adjust_date($row)
    {
        foreach (array('created_at', 'modified_at', 'deleted_at', 'birthday', 'deadline', 'done_at', 'alert_date') as $key) {
            if (isset($row[$key]) && ($row[$key] == '0000-00-00 00:00:00' || $row[$key] == '01.01.0001 0:00:00')) {
                $row[$key] = '';
            }
        }

        return $row;
    }

    /**
     * @param string $model_name
     * @return Model
     */
    public static function Factory($model_name)
    {
        if (class_exists($model_name)) {
            return new $model_name();
        }
    }
}