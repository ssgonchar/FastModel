<?php
/**
 * Created by PhpStorm.
 * User: ssgonchar
 * Date: 18.01.2016
 * Time: 1:02
 */
namespace SSGonchar\FastModel\SEUtil\Db\QueryBuilder;

use SSGonchar\FastModel\SEUtil\Db\QueryBuilder;

class CountQueryBuilder extends QueryBuilder
{
    /**
     * @param $db_connection
     * @return QueryBuilder
     */
    public static function Create($db_connection)
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new CountQueryBuilder($db_connection);
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

        $query .= $this->_prepare_fields_clause();
        $query .= $this->_prepare_from_clause();
        $query .= $this->_prepare_where_clause();

        return $query;
    }

    /**
     *
     */
    function _check_fields_param()
    {
        $this->params['fields'] = array('count(*) as rows');
    }
}