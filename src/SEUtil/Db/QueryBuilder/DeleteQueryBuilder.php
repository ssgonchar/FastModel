<?php
/**
     * Created by PhpStorm.
     * User: ssgonchar
     * Date: 18.01.2016
     * Time: 1:01
     */
namespace SSGonchar\FastModel\SEUtil\Db\QueryBuilder;

use SSGonchar\FastModel\SEUtil\Db\QueryBuilder;

/**
 * Class DeleteQueryBuilder
 * @package SSGonchar\FastModel\SEUtil\Db\QueryBuilder
 */
class DeleteQueryBuilder extends QueryBuilder
{
    /**
     * @param resource $db_connection
     * @return QueryBuilder
     */
    public static function Create($db_connection)
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new DeleteQueryBuilder($db_connection);
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

        $query = 'DELETE ';

        $query .= $this->_prepare_from_clause();
        $query .= $this->_prepare_where_clause();
        $query .= $this->_prepare_order_clause();
        $query .= $this->_prepare_limit_clause();

        return $query;
    }
}