<?php
/**
     * Created by PhpStorm.
     * User: ssgonchar
     * Date: 18.01.2016
     * Time: 1:03
     */
namespace SSGonchar\FastModel\SEUtil\Db\QueryBuilder;

use SSGonchar\FastModel\SEUtil\Db\QueryBuilder;

class CallQueryBuilder extends QueryBuilder
{
    /**
     * @param resource $db_connection
     * @return QueryBuilder
     */
    public static function Create($db_connection)
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new CallQueryBuilder($db_connection);
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

        $query = 'CALL ';

        $query .= $this->_prepare_procedure_clause();
        $query .= $this->_prepare_values_clause();

        return $query;
    }

    /**
     *
     */
    function _prepare_values_clause()
    {
        $query = '(';
        $clause = 'values';

        if (count($this->params[$clause])) {
            for ($i = 0; $i < count($this->params[$clause]); $i++) {
                if ($i > 0) {
                    $query .= ',';
                }

                $query .= $this->_prepare_argument($this->params[$clause][$i]);
            }
        }

        $query .= ')';

        return $query;
    }
}