<?php
/**
     * Created by PhpStorm.
     * User: ssgonchar
     * Date: 18.01.2016
     * Time: 0:53
     */
namespace SSGonchar\FastModel\SEUtil\Db\QueryBuilder;

use SSGonchar\FastModel\SEUtil\Db\QueryBuilder;

/**
 * Class FunctionQueryBuilder
 * @package SSGonchar\FastModel\SEUtil\Db\QueryBuilder
 */
class FunctionQueryBuilder extends QueryBuilder
{
    /**
     * @param resource $db_connection
     * @return QueryBuilder
     */
    public static function Create($db_connection)
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new FunctionQueryBuilder($db_connection);
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

        $query .= $this->_prepare_function_clause();
        $query .= $this->_prepare_values_clause();

        $query .= ' AS result';

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