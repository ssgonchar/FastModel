<?php
/**
     * Created by PhpStorm.
     * User: ssgonchar
     * Date: 18.01.2016
     * Time: 0:59
     */
namespace SSGonchar\FastModel\SEUtil\Db\QueryBuilder;

use SSGonchar\FastModel\SEUtil\Db\QueryBuilder;

/**
 * Class InsertQueryBuilder
 * @package SSGonchar\FastModel\SEUtil\Db\QueryBuilder
 */
class InsertQueryBuilder extends QueryBuilder
{
    var $type;

    /**
     * @param resource $db_connection
     * @return QueryBuilder
     */
    public static function Create($db_connection)
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new InsertQueryBuilder($db_connection);
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

        $fields = $this->_prepare_fields_clause();

        $query = 'INSERT ';

        // 2009.05.06 digi,
        if (!empty($params['ignore'])) {
            $query .= 'IGNORE ';
        }

        if ($fields == '()') {
            // single record. INSERT {table name} SET {field}={value},...
        {
            $this->type = 'single';
        }

            $query .= $this->_prepare_table_clause();
            $values = $this->_prepare_values_clause();
            $query .= $values != '' ? $values : 'VALUES()';
        } else // multiple records. INSERT {table name} ({field list}) VALUES ({value list}), ...
        {
            $this->type = 'multiple';

            $query .= $this->_prepare_table_clause();
            $query .= $this->_prepare_fields_clause();
            $query .= $this->_prepare_values_clause();
        }

        return $query;
    }

    /**
     *
     */
    function _check_fields_param()
    {
        $this->_assure_is_array('fields');
    }

    /**
     *
     */
    function _prepare_fields_clause()
    {
        $query = '(';

        $length = count($this->params['fields']);
        for ($i = 0; $i < $length; $i++) {
            $query .= '`' . $this->params['fields'][$i] . '`';

            if ($i < $length - 1) {
                $query .= ', ';
            } else {
                $query .= ' ';
            }
        }

        $query .= ')';

        return $query;
    }


    /**
     *
     */
    function _prepare_values_clause()
    {
        if ($this->type == 'single') {
            return parent::_prepare_values_clause();
        } else if ($this->type == 'multiple') {
            return $this->_prepare_values_multiple_clause();
        }
    }

    /**
     *
     */
    function _prepare_values_multiple_clause()
    {
        $query = '';
        $clause = 'values';

        if (count($this->params[$clause])) {
            $query .= ' VALUES ';

            for ($i = 0; $i < count($this->params[$clause]); $i++) {
                $values = $this->params[$clause][$i];

                if ($i > 0) {
                    $query .= ',';
                }

                $query .= '(';

                $flag = false;
                foreach ($values as $value) {
                    if ($flag) {
                        $query .= ',';
                    }

                    $query .= $this->_prepare_argument($value);

                    $flag = true;
                }

                $query .= ')';
            }
        }

        //Log::AddLine(LOG_CUSTOM, 'QueryBuilder::_prepare_values_multiple ' . $query);

        return $query;
    }
}