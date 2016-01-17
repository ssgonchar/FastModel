<?php
/**
 * Created by PhpStorm.
 * User: ssgonchar
 * Date: 18.01.2016
 * Time: 0:02
 */

namespace SSGonchar\FastModel\SEUtil\Db;

use SSGonchar\FastModel\SEUtil\Db\QueryBuilder\CallQueryBuilder;
use SSGonchar\FastModel\SEUtil\Db\QueryBuilder\CountQueryBuilder;
use SSGonchar\FastModel\SEUtil\Db\QueryBuilder\DeleteQueryBuilder;
use SSGonchar\FastModel\SEUtil\Db\QueryBuilder\FunctionQueryBuilder;
use SSGonchar\FastModel\SEUtil\Db\QueryBuilder\InsertQueryBuilder;
use SSGonchar\FastModel\SEUtil\Db\QueryBuilder\ReplaceQueryBuilder;
use SSGonchar\FastModel\SEUtil\Db\QueryBuilder\SelectQueryBuilder;
use SSGonchar\FastModel\SEUtil\Db\QueryBuilder\UpdateQueryBuilder;

class QueryBuilder
{
    /**
     * @var array
     */
    var $params;

    /**
     * @var resource
     */
    var $db_connection;

    /**
     * @param $db_connection
     */
    protected function QueryBuilder($db_connection)
    {
        $this->db_connection = $db_connection;
    }

    /**
     * @param $db_connection
     * @return QueryBuilder
     */

    public static function Create($db_connection)
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new QueryBuilder($db_connection);
        }
        return $instance;
    }

    /**
     * <code>
     *      array(
     *          'fields' => array(
     *              'locale_constants.*',
     *              'locale_constants.name',
     *              'locale_constants.description',
     *              'locale_constants.short_name as locale',
     *              'locale_constants.language'
     *          ),
     *          'join' => array(
     *              array(
     *                  'table' => 'constants',
     *                  'conditions' => 'constants.id = locale_constants.constant_id'
     *              ),
     *              array(
     *                  'table' => 'locales,
     *                  'conditions' => 'locales.id = locale_constants.locale_id'
     *              )
     *          ),
     *          'where' => array(
     *              'conditions' => 'constants.name like ?',
     *              'arguments' => array('c\_%')
     *          )
     *      )
     * </code>
     *
     * @param $params
     * @return string
     * @internal param array $param query string*
     *     query string
     *     table string
     *     procedure string
     *     ignore bool
     *     SQL_CALC_FOUND_ROWS
     *
     *
     *     from array
     *     fields array
     *     values array
     *     join array
     *       'table' string
     *       'type' string
     *       'conditions' string
     *       ('select')
     *     where mixed
     *       'conditions' string
     *       'arguments' array
     *       ('select', 'update', 'delete', 'count')
     *
     *
     *     group array
     *     order array
     *     limit mixed
     *
     *
     *       ('select', 'update', 'delete')
     *
     */
    function Prepare($params)
    {
        $this->params = $params;

        $this->_check_query_param();

        switch ($this->params['query']) {
            case 'select':
                $q = SelectQueryBuilder::Create($this->db_connection);
                break;

            case 'update':
                $q = UpdateQueryBuilder::Create($this->db_connection);
                break;

            case 'insert':
                $q = InsertQueryBuilder::Create($this->db_connection);
                break;

            case 'delete':
                $q = DeleteQueryBuilder::Create($this->db_connection);
                break;

            case 'count':
                $q = CountQueryBuilder::Create($this->db_connection);
                break;

            case 'call':
                $q = CallQueryBuilder::Create($this->db_connection);
                break;

            case 'function':
                $q = FunctionQueryBuilder::Create($this->db_connection);
                break;

            case 'replace':
                $q = ReplaceQueryBuilder::Create($this->db_connection);
                break;

            default:
                $q = SelectQueryBuilder::Create($this->db_connection);
                break;
        }
        return $q->Prepare($this->params);
    }

    /**
     *
     */
    function _check_params()
    {
        if ($this->params['query'] == 'call') {
            $this->_check_procedure_param();
        } else if ($this->params['query'] == 'function') {
            $this->_check_function_param();
        } else {
            $this->_check_table_param();
            $this->_check_from_param();
        }

        $this->_check_fields_param();
        $this->_check_values_param();
        $this->_check_join_param();
        $this->_check_where_param();
        $this->_check_having_param();
        $this->_check_group_param();
        $this->_check_order_param();
        $this->_check_limit_param();
    }

    /**
     *
     */
    function _check_query_param()
    {
        $clause = 'query';

        if (!is_array($this->params)) {
            $this->params = array();
        }

        if (!array_key_exists($clause, $this->params)) {
            $this->params[$clause] = 'select';
        }

        if (!in_array($this->params[$clause], array('select', 'update', 'insert', 'delete', 'count', 'call', 'function', 'replace'))) {
            $this->params[$clause] = 'select';
        }
    }

    /**
     *
     */
    function _check_fields_param()
    {
    }

    /**
     *
     */
    function _check_table_param()
    {
        $this->_assure_is_string('table');
        $this->_assure_is_not_empty('table');
    }

    /**
     *
     */
    function _check_procedure_param()
    {
        $this->_assure_is_string('procedure');
        $this->_assure_is_not_empty('procedure');
    }

    /**
     *
     */
    function _check_function_param()
    {
        $this->_assure_is_string('function');
        $this->_assure_is_not_empty('function');
    }

    /**
     *
     */
    function _check_from_param()
    {
        $this->_assure_is_array('from');

        if (!count($this->params['from'])) {
            $this->params['from'] = array($this->params['table']);
        }
    }

    /**
     *
     */
    function _check_where_param()
    {
        $clause = 'where';

        $this->_assure_is_array($clause);

        if (array_key_exists('conditions', $this->params[$clause])) {
            $this->params[$clause] = array($this->params[$clause]);
        }

        $length = count($this->params[$clause]);
        for ($i = 0; $i < $length; $i++) {
            if (!is_array($this->params[$clause][$i])) {
                $this->params[$clause][$i] = array('conditions' => $this->params[$clause][$i]);
            }

            if (!array_key_exists('arguments', $this->params[$clause][$i])) {
                $this->params[$clause][$i]['arguments'] = array();
            }

            if (!is_array($this->params[$clause][$i]['arguments'])) {
                $this->params[$clause][$i]['arguments'] = array($this->params[$clause][$i]['arguments']);
            }

            if (preg_match_all('(\?)', $this->params[$clause][$i]['conditions'], $res) != count($this->params[$clause][$i]['arguments'])) {
                trigger_error('Wrong number of arguments for WHERE condition. Params: ' . var_export($this->params, true));
            }
        }
    }

    /**
     *
     */
    function _check_having_param()
    {
        $clause = 'having';

        $this->_assure_is_array($clause);

        if (array_key_exists('conditions', $this->params[$clause])) {
            $this->params[$clause] = array($this->params[$clause]);
        }

        $length = count($this->params[$clause]);
        for ($i = 0; $i < $length; $i++) {
            if (!is_array($this->params[$clause][$i])) {
                $this->params[$clause][$i] = array('conditions' => $this->params[$clause][$i]);
            }

            if (!array_key_exists('arguments', $this->params[$clause][$i])) {
                $this->params[$clause][$i]['arguments'] = array();
            }

            if (!is_array($this->params[$clause][$i]['arguments'])) {
                $this->params[$clause][$i]['arguments'] = array($this->params[$clause][$i]['arguments']);
            }

            if (preg_match_all('(\?)', $this->params[$clause][$i]['conditions'], $res) != count($this->params[$clause][$i]['arguments'])) {
                trigger_error('Wrong number of arguments for HAVING condition. Params: ' . var_export($this->params, true));
            }
        }
    }

    /**
     *
     */
    function _check_join_param()
    {
        $clause = 'join';

        $this->_assure_is_array($clause);

        if (array_key_exists('table', $this->params[$clause])) {
            $this->params[$clause] = array($this->params[$clause]);
        }

        $length = count($this->params[$clause]);
        for ($i = 0; $i < $length; $i++) {
            if (!array_key_exists('table', $this->params[$clause][$i])) {
                trigger_error('No table provided for JOIN clause. Params: ' . var_export($this->params, true));
            }

            if (is_array($this->params[$clause][$i]['table'])) {
                trigger_error('Array passed for table parameter where string expected for JOIN clause. Params: ' . var_export($this->params, true));
            }

            if (!is_array($this->params[$clause][$i])) {
                $this->params[$clause][$i] = array('conditions' => $this->params[$clause][$i]);
            }

            if (!array_key_exists('type', $this->params[$clause][$i])) {
                $this->params[$clause][$i]['type'] = 'INNER';
            }

            if (!array_key_exists('arguments', $this->params[$clause][$i])) {
                $this->params[$clause][$i]['arguments'] = array();
            }

            if (!is_array($this->params[$clause][$i]['arguments'])) {
                $this->params[$clause][$i]['arguments'] = array($this->params[$clause][$i]['arguments']);
            }

            if (preg_match_all('(\?)', $this->params[$clause][$i]['conditions'], $res) != count($this->params[$clause][$i]['arguments'])) {
                trigger_error('Wrong number of arguments for JOIN condition. Params: ' . var_export($this->params, true));
            }
        }
    }

    /**
     *
     */
    function _check_values_param()
    {
        $this->_assure_is_array('values');
    }

    /**
     *
     */
    function _check_order_param()
    {
        $this->_assure_is_array('order');
    }

    /**
     *
     */
    function _check_group_param()
    {
        $this->_assure_is_array('group');
    }

    /**
     *
     */
    function _check_limit_param()
    {
        $clause = 'limit';
        $lower = 'lower';
        $number = 'number';

        $this->_assure_is_array($clause);

//      if (!array_key_exists($clause, $this->params))
//      {
//          $this->params[$clause] = array();
//      }

        if (!array_key_exists($number, $this->params[$clause])) {
            if (count($this->params[$clause]) > 1) {
                if (array_key_exists(0, $this->params[$clause]))
                    $this->params[$clause][$lower] = intval($this->params[$clause][0]);
                else
                    $this->params[$clause][$lower] = 0;

                if (array_key_exists(1, $this->params[$clause]))
                    $this->params[$clause][$number] = intval($this->params[$clause][1]);
                else
                    $this->params[$clause][$number] = 0;
            } else if (count($this->params[$clause]) > 0) {
                $this->params[$clause][$number] = intval($this->params[$clause][0]);
            } else {
                $this->params[$clause][$number] = 0;
                $this->params[$clause][$lower] = 0;
            }
        }

        $this->params[$clause][$number] = intval($this->params[$clause][$number]);
        if ($this->params[$clause][$number] < 0)
            $this->params[$clause][$number] = 0;

        if (!array_key_exists($lower, $this->params[$clause])) {
            $this->params[$clause][$lower] = 0;
        }
        $this->params[$clause][$lower] = intval($this->params[$clause][$lower]);
        if ($this->params[$clause][$lower] < 0)
            $this->params[$clause][$lower] = 0;

        $this->params[$clause] = array(
            $lower => $this->params[$clause][$lower],
            $number => $this->params[$clause][$number]);
    }

    /**
     *
     *
     *
     *
     * @param string $param
     */
    function _assure_is_string($param)
    {
        if (!array_key_exists($param, $this->params)) {
            $this->params[$param] = '';
        }

        if (is_array($this->params[$param])) {
            trigger_error("Array passed for '$param' parameter where string expected. Params: '" . var_export($this->params, true));
        }
    }

    /**
     *
     *
     *
     *
     *
     *
     *
     * @param mixed $param
     */
    function _assure_is_not_empty($param)
    {
        if (is_string($this->params[$param])) {
            if ($this->params[$param] == '') {
                trigger_error("Value passed for '$param' parameter is empty. Params: " . var_export($this->params, true));
            }
        } else if (is_array($this->params[$param])) {
            if (count($this->params[$param]) == 0) {
                trigger_error("Array passed for '$param' parameter is empty. Params: " . var_export($this->params, true));
            }
        }
    }

    /**
     *
     *
     *
     *
     *
     *
     * @param mixed $param
     */
    function _assure_is_array($param)
    {
        if (!array_key_exists($param, $this->params)) {
            $this->params[$param] = array();
        }

        if (!is_array($this->params[$param])) {
            $this->params[$param] = array($this->params[$param]);
        }
    }

    /**
     *
     *
     * @param array $value
     * @return string
     */
    function _parse_condition($value)
    {
        $result = '';

        $explode = explode('?', $value['conditions']);
        for ($i = 0; $i < count($explode); $i++) {
            $result .= $explode[$i];
            if ($i < count($explode) - 1) {
                $result .= $this->_prepare_argument($value['arguments'][$i]);
            }
        }

        return $result;
    }

    /**
     * @param $argument
     * @return string
     * @internal param string $value
     */
    function _prepare_argument($argument)
    {
        if ($argument === 'NULL VALUE!') {
            return 'NULL';
        }

        if ($argument === 'NOW()!') {
            return 'NOW()';
        }

        return '\'' . mysqli_real_escape_string($this->db_connection, $argument) . '\'';
    }

    /**
     *
     */
    function _prepare_fields_clause()
    {
        $query = '';

        $length = count($this->params['fields']);
        for ($i = 0; $i < $length; $i++) {
            $query .= $this->params['fields'][$i];

            if ($i < $length - 1) {
                $query .= ', ';
            } else {
                $query .= ' ';
            }
        }

//        Log::AddLine(LOG_CUSTOM, 'QueryBuilder::_prepare_fields_clause ' . $query);

        return $query;
    }

    /**
     *
     */
    function _prepare_table_clause()
    {
        //Log::AddLine(LOG_CUSTOM, 'QueryBuilder::_prepare_table_clause ' . $this->params['table']);

        return $this->params['table'] . ' ';
    }

    /**
     *
     */
    function _prepare_procedure_clause()
    {
        return $this->params['procedure'] . ' ';
    }

    /**
     *
     */
    function _prepare_function_clause()
    {
        return $this->params['function'] . ' ';
    }

    /**
     *
     */
    function _prepare_from_clause()
    {
        $query = '';

        if (count($this->params['from'])) {
            $query .= 'FROM ';

            $length = count($this->params['from']);
            for ($i = 0; $i < $length; $i++) {
                $query .= $this->params['from'][$i];

                if ($i < $length - 1) {
                    $query .= ', ';
                } else {
                    $query .= ' ';
                }
            }
        }

        return $query;
    }

    /**
     *
     */
    function _prepare_values_clause()
    {
        $query = '';
        $clause = 'values';

        if (count($this->params[$clause])) {
            $query .= 'SET ';

            $flag = false;
            foreach ($this->params[$clause] as $key => $value) {
                if ($flag) {
                    $query .= ', ';
                }

                $query .= '`' . $key . '`' . ' = ' . $this->_prepare_argument($value);

                $flag = true;
            }

            $query .= ' ';
        }

        return $query;
    }

    /**
     *
     */
    function _prepare_where_clause()
    {
        $query = '';

        if (count($this->params['where'])) {
            $query .= 'WHERE ';

            $flag = false;
            foreach ($this->params['where'] as $value) {
                if ($flag) {
                    $query .= ' AND ';
                }

                $query .= '(' . $this->_parse_condition($value) . ')';

                $flag = true;
            }

            $query .= ' ';
        }

        return $query;
    }

    /**
     *
     */
    function _prepare_having_clause()
    {
        $query = '';

        if (count($this->params['having'])) {
            $query .= 'HAVING ';

            $flag = false;
            foreach ($this->params['having'] as $value) {
                if ($flag) {
                    $query .= ' AND ';
                }

                $query .= '(' . $this->_parse_condition($value) . ')';

                $flag = true;
            }

            $query .= ' ';
        }

        return $query;
    }

    /**
     *
     */
    function _prepare_join_clause()
    {
        $query = '';

        if (count($this->params['join'])) {
            foreach ($this->params['join'] as $value) {
                $query .= $value['type'] . ' JOIN ' . $value['table'] . ' ON (' . $this->_parse_condition($value) . ') ';
            }
        }

        return $query;
    }

    /**
     *
     */
    function _prepare_order_clause()
    {
        $query = '';

        if (count($this->params['order'])) {
            $query .= 'ORDER BY ';

            $length = count($this->params['order']);
            for ($i = 0; $i < $length; $i++) {
                $query .= $this->params['order'][$i];

                if ($i < $length - 1) {
                    $query .= ', ';
                } else {
                    $query .= ' ';
                }
            }
        }

        return $query;
    }

    /**
     *
     */
    function _prepare_group_clause()
    {
        $query = '';

        if (count($this->params['group']) && !empty($this->params['group'][0])) {
            $query .= 'GROUP BY ';

            $length = count($this->params['group']);
            for ($i = 0; $i < $length; $i++) {
                $query .= $this->params['group'][$i];

                if ($i < $length - 1) {
                    $query .= ', ';
                } else {
                    $query .= ' ';
                }
            }
        }

        return $query;
    }

    /**
     *
     */
    function _prepare_limit_clause()
    {
        $query = '';

        if (count($this->params['limit'])) {
            $lower = $this->params['limit']['lower'];
            $number = $this->params['limit']['number'];

            if ($lower > 0 || $number > 0) {
                $query .= 'LIMIT ';
                if ($lower == 0) {
                    $query .= $number;
                } else {
                    $query .= "$lower, $number";
                }
            }
        }

        return $query;
    }
}
