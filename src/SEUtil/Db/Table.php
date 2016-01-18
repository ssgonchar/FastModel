<?php
/**
     * Created by PhpStorm.
     * User: ssgonchar
     * Date: 18.01.2016
     * Time: 0:01
     */

namespace SSGonchar\FastModel\SEUtil\Db;

/**
 * Class Table
 * @package SSGonchar\FastModel\SEUtil\Db
 */
class Table
{
    /**
     *
     *
     * @var string
     */
    var $table_name;


    /**
     *
     *
     * @var string
     */
    var $index_field;

    /**
     *
     *
     * @var string
     */
    var $order_by;

    /**
     *
     *
     * @var DatabaseConnection
     */
    var $db;

    /**
     *
     *
     * @var QueryBuilder
     */
    var $query;

    /**
     *
     *
     * @var array
     */
    var $connection_settings;

    /**
     *
     *
     * @var boolean
     */
    var $is_connected = false;

    /**
     *
     *
     * @var mixed
     */
    var $connection_time_zone = DB_TIME_ZONE;
    private $name_field;

    /**
     *
     *
     * @param string $table_name
     * @param string $connection_settings
     * @see DatabaseConnection::Create()
     */
    function __construct($table_name, $connection_settings)
    {
        $this->index_field = 'id';
        $this->name_field = 'name';

        $this->table_name = $table_name;
        $this->connection_settings = $connection_settings;
    }

    /**
     *
     */
    function ConnectDatabase()
    {
        if ($this->is_connected) {
            return;
        }

        $this->is_connected = true;

        if (!empty($this->connection_time_zone)) {
            $this->connection_settings['time_zone'] = $this->connection_time_zone;
        }

        $this->db = DatabaseConnection::Create($this->connection_settings);
        $this->db->OpenConnection();

        $this->query = QueryBuilder::Create($this->db->connection);
    }


    /**
     * @param resource $resource
     * @return array|null
     */
    private function _fetch_row($resource)
    {
        return mysqli_fetch_assoc($resource);
    }


    /**
     * @param resource $resource
     * @return array
     */
    public function _fetch_array($resource) //!must be private
    {
        $res = array();
        $count = mysqli_num_rows($resource);
        while ($count-- > 0) {
            $res[] = mysqli_fetch_assoc($resource);
        }

        return $res;
    }

    /**
     * @param $resource
     * @return array|null
     */
    private function _fetch_raw_array($resource)
    {
        return mysqli_fetch_array($resource);
    }

    /**
     * @return array
     */
    function _fetch_multi_set()
    {
        $res = array();

        do {
            if ($result = mysqli_store_result($this->db->connection)) {
                $set = array();

                while ($row = mysqli_fetch_assoc($result)) {
                    $set[] = $row;
                }
                mysqli_free_result($result);

                $res[] = $set;
            }

            $more_result = mysqli_more_results($this->db->connection);

            if ($more_result) {
                $more_result = mysqli_next_result($this->db->connection);
            }
        } while ($more_result);

        return $res;
    }

    /**
     * @param $params
     * @return resource
     */
    private function _exec_query($params)
    {
        $this->ConnectDatabase();
        return $this->db->query($this->query->Prepare($params));
    }

    /**
     *
     *
     * @param array $params
     * @return resource
     */
    private function _exec_multi_query($params)
    {
        $this->ConnectDatabase();
        return $this->db->multiquery($this->query, $params);
    }

    /**
     * @param $query
     * @return resource
     */
    public function _exec_raw_query($query) //! must be private
    {
        //print_r($query);
        $this->ConnectDatabase();
        return $this->db->query($query);
    }


    /**
     * @param $params
     * @return array
     */
    private function _assure_is_array($params)
    {
        if (!is_array($params)) {
            return array($params);
        }

        return $params;
    }


    /**
     * @param array $arg
     * @return null
     */
    public function SelectSingle($arg = array())
    {
        $params = $arg; //func_num_args() > 0 ? func_get_arg(0) : array();
        $params = $this->_assure_is_array($params);

        $params['table'] = $this->table_name;
        $params['limit'] = 1;
        if (!array_key_exists('order', $params) && isset($this->order_by)) {
            $params['order'] = $this->order_by;
        }

        $resource = $this->_exec_query($params);
        $result = $this->_fetch_array($resource);

        if (count($result)) {
                    return $result[0];
        }

        return null;
    }


    /**
     * @param array $arg
     * @return array
     */
    public function SelectList($arg = array())
    {
        $params = $arg; //func_num_args() > 0 ? func_get_arg(0) : array();
        $params = $this->_assure_is_array($params);

        $params['table'] = $this->table_name;
        if (!array_key_exists('order', $params) && isset($this->order_by)) {
            $params['order'] = $this->order_by;
        }

        $resource = $this->_exec_query($params);
        return $this->_fetch_array($resource);
    }


    /**
     *
     *
     *
     *
     *
     * @param integer $id
     * @return array
     */
    public function Select($id)
    {
        if (!isset($id)) {
            trigger_error('Parameter "id" must be specified!');
            return;
        }

        $params = array('where' => array('conditions' => $this->table_name . '.' . $this->index_field . '=?', 'arguments' => $id));

        $result = $this->SelectList($params);

        if (count($result)) {
                    return $result[0];
        }

        return null;
    }


    /**
     * @param $field
     * @param array $arg
     * @return array
     */
    public function SelectListAssoc($field, $arg = array())
    {
        $params = $arg; //func_num_args() > 1 ? func_get_arg(1) : array();
        $params = $this->_assure_is_array($params);

        $arr = $this->SelectList($params);

        $assoc = array();

        foreach ($arr as $a) {
            $assoc[$a[$this->index_field]] = $a[$field];
        }

        return $assoc;
    }


    /**
     * @param array $arg
     * @return mixed
     */
    public function Count($arg = array())
    {
        $params = $arg; //func_num_args() > 0 ? func_get_arg(0) : array();
        $params = $this->_assure_is_array($params);

        $params['query'] = 'count';
        $params['table'] = $this->table_name;

        $resource = $this->_exec_query($params);
        $arr = $this->_fetch_row($resource);
        return $arr['rows'];
    }


    /**
     *
     * @see DatabaseConnection::FoundRows()
     *
     * @return integer
     */
    public function FoundRows()
    {
        return $this->db->FoundRows();
    }


    /**
     * @param array $arg
     * @return int
     */
    public function DeleteList($arg = array())
    {
        $params = $arg; //func_num_args() > 0 ? func_get_arg(0) : array();
        $params = $this->_assure_is_array($params);

        $params['query'] = 'delete';
        $params['table'] = $this->table_name;

        $this->_exec_query($params);

        return $this->db->AffectedRows();
    }

    /**
     *
     *
     * @param integer $id
     * @return integer
     */
    public function Delete($id)
    {
        if (!isset($id)) {
            trigger_error('Parameter "id" must be specified!');
            return;
        }

        $params = array('where' => array('conditions' => 'id=?', 'arguments' => $id));

        return $this->DeleteList($params);
    }


    /**
     * @param array $arg
     * @return bool
     */
    public function Exists($arg = array())
    {
        $params = $arg; //func_num_args() > 0 ? func_get_arg(0) : array();
        $params = $this->_assure_is_array($params);

        return ($this->Count($params) > 0 ? true : false);
    }


    /**
     *
     *
     * @param array $values
     * @param boolean $ignore_unique_error
     * @return integer id
     * @version 2009.05.05 by digi
     */
    public function Insert($values, $ignore_unique_error = false)
    {
        $params = array();
        $params['query'] = 'insert';
        $params['table'] = $this->table_name;
        $params['values'] = $values;

        if (!empty($ignore_unique_error)) {
            $params['ignore'] = true;
        }

        $this->_exec_query($params);
        return $this->db->Identity();
    }

    /**
     *
     *
     * @param array $values
     * @param array $fields
     * @param boolean $ignore_unique_error
     * @return integer id
     */
    function InsertList($fields, $values, $ignore_unique_error = false)
    {
        $params = array();
        $params['query'] = 'insert';
        $params['table'] = $this->table_name;
        $params['fields'] = $fields;
        $params['values'] = $values;

        if (!empty($ignore_unique_error)) {
            $params['ignore'] = true;
        }

        $this->_exec_query($params);
        return $this->db->Identity();
    }

    /**
     *
     *
     * @param array $values
     * @return integer id
     * @version 2010.05.04 by digi
     */
    public function Replace($values)
    {
        $params = array();
        $params['query'] = 'replace';
        $params['table'] = $this->table_name;
        $params['values'] = $values;

        $this->_exec_query($params);
        return $this->db->Identity();
    }

    /**
     *
     *
     * @param array $values
     * @param array $fields
     * @return integer id
     * @version 2010.05.04 by digi
     */
    function ReplaceList($fields, $values)
    {
        $params = array();
        $params['query'] = 'replace';
        $params['table'] = $this->table_name;
        $params['fields'] = $fields;
        $params['values'] = $values;

        $this->_exec_query($params);
        return $this->db->Identity();
    }


    /**
     *
     *
     * @param integer $id
     * @param array $values
     * @param boolean $ignore_unique_error
     */
    public function Update($id, $values, $ignore_unique_error = false)
    {
        if (!isset($id)) {
            trigger_error('Parameter "id" must be specified!');
            return;
        }

        $params = array('where' => array('conditions' => 'id=?', 'arguments' => $id));
        $params['values'] = $values;

        if (!empty($ignore_unique_error)) {
            $params['ignore'] = true;
        }

        $this->UpdateList($params, $ignore_unique_error);
    }


    /**
     *
     *
     * @param mixed $params
     * @return integer
     * @param boolean $ignore_unique_error
     */
    public function UpdateList($params = array(), $ignore_unique_error = false)
    {
        $params = $this->_assure_is_array($params);

        $params['query'] = 'update';
        $params['table'] = $this->table_name;

        if (!empty($ignore_unique_error)) {
            $params['ignore'] = true;
        }

        $this->_exec_query($params);

        return $this->db->AffectedRows();
    }

    /**
     * @param $name
     * @param $values
     * @return resource
     */
    function CallStoredProcedure($name, $values)
    {
        //print_r($name);
        //print_r($values);
        $params['query'] = 'call';
        $params['procedure'] = $name;
        $params['values'] = $values;
        //print_r(__FUNCTION__);
        //print_r($values);
        return $this->_exec_multi_query($params);
        //$result = $this->_exec_multi_query($params);
        //if (!$result) return null;
        //return $this->_fetch_multi_set();
    }

    /**
     * @param $name
     * @param $values
     * @return null
     */
    function CallStoredFunction($name, $values)
    {
        $params['query'] = 'function';
        $params['function'] = $name;
        $params['values'] = $values;

        $resource = $this->_exec_query($params);
        $result = $this->_fetch_array($resource);

        if (count($result)) {
            return $result[0];
        }

        return null;
    }
}