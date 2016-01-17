<?php
/**
 * Created by PhpStorm.
 * User: ssgonchar
 * Date: 18.01.2016
 * Time: 0:55
 */
namespace SSGonchar\FastModel\SEUtil\Db\QueryBuilder;

use SSGonchar\FastModel\SEUtil\Db\QueryBuilder;

class SelectQueryBuilder extends QueryBuilder
{
    /**
     * @param $db_connection
     * @return SelectQueryBuilder
     */
    public static function Create($db_connection)
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new SelectQueryBuilder($db_connection);
        }
        return $instance;
    }

    /**
     * @param $params
     * @return string
     */
    function Prepare($params)
    {
        $this->params = $params;

        $this->_check_params();

        $query = 'SELECT ';

        if (isset($params['SQL_CALC_FOUND_ROWS']) && $params['SQL_CALC_FOUND_ROWS']) {
            $query .= ' SQL_CALC_FOUND_ROWS ';
        }

        $query .= $this->_prepare_fields_clause();
        $query .= $this->_prepare_from_clause();
        $query .= $this->_prepare_join_clause();
        $query .= $this->_prepare_where_clause();
        $query .= $this->_prepare_group_clause();
        $query .= $this->_prepare_having_clause();
        $query .= $this->_prepare_order_clause();
        $query .= $this->_prepare_limit_clause();

        return $query;
    }

    /**
     *
     */
    function _check_fields_param()
    {
        $this->_assure_is_array('fields');

        if (!count($this->params['fields'])) {
            $this->params['fields'][] = ($this->params['table'] != '' ? $this->params['table'] . '.' : '') . '*';
        }
    }
}