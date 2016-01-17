<?php
/**
 * Created by PhpStorm.
 * User: ssgonchar
 * Date: 18.01.2016
 * Time: 0:57
 */
namespace SSGonchar\FastModel\SEUtil\Db\QueryBuilder;

use SSGonchar\FastModel\SEUtil\Db\QueryBuilder;

class UpdateQueryBuilder extends QueryBuilder
{
    /**
     * @param $db_connection
     * @return QueryBuilder
     */
    public static function Create($db_connection)
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new UpdateQueryBuilder($db_connection);
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

        $query = 'UPDATE ';

        if (!empty($params['ignore'])) {
            $query .= 'IGNORE ';
        }

        $query .= $this->_prepare_table_clause();
        $query .= $this->_prepare_values_clause();
        $query .= $this->_prepare_where_clause();
        $query .= $this->_prepare_order_clause();
        $query .= $this->_prepare_limit_clause();

        return $query;
    }
}