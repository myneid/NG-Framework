<?php

/**
 * NG Framework
 * Version 0.1 Beta
 * Copyright (c) 2012, Nick Gejadze
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy 
 * of this software and associated documentation files (the "Software"), 
 * to deal in the Software without restriction, including without limitation 
 * the rights to use, copy, modify, merge, publish, distribute, sublicense, 
 * and/or sell copies of the Software, and to permit persons to whom the 
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included 
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, 
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A 
 * PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR 
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, 
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, 
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace NG;

/**
 * Query
 * @package NG
 * @subpackage library
 * @version 0.1
 * @copyright (c) 2012, Nick Gejadze
 */
class Query {

    /**
     * $query
     * Holds full query string
     * @access protected
     * @var string
     */
    protected $query = '';

    /**
     * $select
     * Holds select variables, can be string or array
     * @access private
     * @var array|string
     */
    private $select;

    /**
     * $table
     * Holds table name
     * @access private
     * @var string
     */
    private $table;

    /**
     * $insertData
     * Holds inset data
     * @access private
     * @var array
     */
    private $insertData;

    /**
     * $updateData
     * Holds update data
     * @access private
     * @var array
     */
    private $updateData;

    /**
     * $deleteTable
     * Holds boolean value if delete query is requested or not
     * @access private
     * @var boolean
     */
    private $deleteTable;

    /**
     * $from
     * Holds from string or array
     * @access private 
     * @var string|array
     */
    private $from;

    /**
     * $join
     * Holds join data as an array
     * @access private
     * @var array
     */
    private $join;

    /**
     * $innerJoin
     * Holds innerJoin data as an array
     * @access private
     * @var array
     */
    private $innerJoin;

    /**
     * $leftJoin
     * Holds leftJoin data as an array
     * @access private
     * @var array
     */
    private $leftJoin;

    /**
     * $rightJoin
     * Holds rightJoin data as an array
     * @access private
     * @var array
     */
    private $rightJoin;

    /**
     * $where
     * Holds where clause
     * @access private
     * @var string
     */
    private $where;

    /**
     * $groupBy
     * Holds groupdBy clause
     * @access private
     * @var string
     */
    private $groupBy;

    /**
     * $having
     * Holds having clause
     * @access private
     * @var string
     */
    private $having;

    /**
     * select()
     * Sets Select Object, Returns Build()
     * @see build()
     * @access public
     * @param string $select
     * @return object
     */
    public function select($select = "*") {
        $this->select = $select;
        return $this->build();
    }

    /**
     * insert()
     * Sets Table name and insert data
     * @access public
     * @see build()
     * @param string $table
     * @param array $data
     * @return object
     * @throws NG_Exception
     */
    public function insert($table, $data) {
        $this->table = $table;
        if (!isset($data) or !is_array($data)):
            throw new NG_Exception("Insert Values are required to build query");
        endif;
        $this->insertData = $data;
        return $this->build();
    }

    /**
     * update()
     * Sets Table name and data to update
     * @access public
     * @see build()
     * @param string $table
     * @param type $data
     * @return object
     * @throws NG_Exception
     */
    public function update($table, $data) {
        $this->table = $table;
        if (!isset($data) or !is_array($data)):
            throw new NG_Exception("Update Values are required to build query");
        endif;
        $this->updateData = $data;
        return $this->build();
    }

    /**
     * delete()
     * Sets Delete flag object
     * @access public
     * @see build()
     * @return object
     */
    public function delete() {
        $this->deleteTable = true;
        return $this->build();
    }

    /**
     * from()
     * Sets from object
     * @access public
     * @see build()
     * @param string $from
     * @return object
     * @throws NG_Exception
     */
    public function from($from = null) {
        if (!isset($from)):
            throw new NG_Exception("FROM is Required to build query");
        endif;
        $this->from = $from;
        return $this->build();
    }

    /**
     * join()
     * Sets join object with table and clause
     * @access public
     * @see build()
     * @param string $table
     * @param string $clause
     * @return object
     */
    public function join($table, $clause) {
        $this->join['table'] = $table;
        $this->join['clause'] = $clause;
        return $this->build();
    }

    /**
     * innerJoin()
     * Sets inner join object with table and clause
     * @access public
     * @see build()
     * @param string $table
     * @param string $clause
     * @return object
     */
    public function innerJoin($table, $clause) {
        $this->innerJoin['table'] = $table;
        $this->innerJoin['clause'] = $clause;
        return $this->build();
    }

    /**
     * leftKoin()
     * Sets left join object with table and clause
     * @access public
     * @see build()
     * @param string $table
     * @param string $clause
     * @return object
     */
    public function leftJoin($table, $clause) {
        $this->leftJoin['table'] = $table;
        $this->leftJoin['clause'] = $clause;
        return $this->build();
    }

    /**
     * rightJoin()
     * Sets right join object with table and clause
     * @access public
     * @see build()
     * @param string $table
     * @param string $clause
     * @return object
     */
    public function rightJoin($table, $clause) {
        $this->rightJoin['table'] = $table;
        $this->rightJoin['clause'] = $clause;
        return $this->build();
    }

    /**
     * where()
     * Sets where object
     * example: where("foo = ?", "bar")
     * @see build()
     * @param string $where
     * @param string $value
     * @return object
     */
    public function where($where, $value = null) {
        $this->where = str_replace("?", "'" . addslashes($value) . "'", $where);
        return $this->build();
    }

    /**
     * group()
     * Sets groupBy object
     * @see build()
     * @param string $field
     * @return object
     */
    public function group($field) {
        $this->groupBy = $field;
        return $this->build();
    }

    /**
     * having()
     * Set having object
     * example: having("foo > ?", "bar")
     * @param string $condition
     * @param string $value
     * @return object
     */
    public function having($condition, $value = null) {
        $this->having = str_replace("?", "'" . addslashes($value) . "'", $condition);
        return $this->build();
    }

    /**
     * order()
     * Sets orderBy Object
     * Example: order("FOO", "DESC") or order("RAND(" . date("Ymd") . ")", "DESC")
     * @access public
     * @see build()
     * @param string $field
     * @param string $clause
     * @return object
     */
    public function order($field, $clause) {
        if (strpos($field, "(") === false):
            $field = "`" . $field . "`";
        endif;
        $this->orderBy = $field . " " . $clause;
        return $this->build();
    }

    /**
     * limit()
     * Sets limit object
     * @access public
     * @see build()
     * @param int $int
     * @return object
     */
    public function limit($int) {
        $this->limit = $int;
        return $this->build();
    }

    /**
     * build()
     * Builds query and sets as query object
     * @access private
     * @return \NG\Query
     */
    private function build() {
        if (isset($this->table) and isset($this->insertData)):
            $this->query = "INSERT INTO `" . $this->table . "` ";
            $fields = implode("`, `", array_keys($this->insertData));
            $this->query.= "(`" . $fields . "`)";
            $values = implode("', '", $this->insertData);
            $this->query.= " VALUES ('" . $values . "')";
        endif;
        if (isset($this->table) and isset($this->updateData)):
            $this->query = "UPDATE `" . $this->table . "` SET ";
            if (is_array($this->updateData)):
                foreach ($this->updateData as $field => $value):
                    $this->query .= "`" . $field . "` = '" . $value . "', ";
                endforeach;
                $this->query = substr($this->query, 0, -2) . " ";
            endif;
        endif;
        if (isset($this->deleteTable)):
            $this->query = "DELETE ";
        endif;
        if (isset($this->select)):
            if (is_array($this->select)):
                $this->select = implode("`, `", $this->select);
            endif;
            if (strpos($this->select, "(") === false):
                $this->select = ($this->select == "*") ? $this->select : "`" . $this->select . "`";
            endif;
            $this->query = "SELECT " . $this->select . " ";
        endif;
        if (isset($this->from)):
            if (is_array($this->from)):
                $this->from = implode("`, `", $this->from);
            endif;
            $this->query.= "FROM `" . $this->from . "` ";
        endif;
        if (isset($this->join)):
            $this->query.="JOIN `" . $this->join['table'] . "` ON " . $this->join['clause'] . " ";
        endif;
        if (isset($this->innerJoin)):
            $this->query.="INNER JOIN `" . $this->innerJoin['table'] . "` ON " . $this->innerJoin['clause'] . " ";
        endif;
        if (isset($this->leftJoin)):
            $this->query.="LEFT JOIN `" . $this->leftJoin['table'] . "` ON " . $this->leftJoin['clause'] . " ";
        endif;
        if (isset($this->rightJoin)):
            $this->query.="RIGHT JOIN `" . $this->rightJoin['table'] . "` ON " . $this->rightJoin['clause'] . " ";
        endif;
        if (isset($this->where)):
            $this->query.="WHERE " . $this->where . " ";
        endif;
        if (isset($this->having)):
            $this->query.="HAVING " . $this->having . " ";
        endif;
        if (isset($this->groupBy)):
            if (is_array($this->groupBy)):
                $this->groupBy = implode("`, `", $this->groupBy);
            endif;
            $this->query.="GROUP BY `" . $this->groupBy . "` ";
        endif;
        if (isset($this->orderBy)):
            $this->query.="ORDER BY " . $this->orderBy . " ";
        endif;
        if (isset($this->limit)):
            $this->query.="LIMIT " . $this->limit;
        endif;
        $this->unsetObjects();
        trim($this->query);
        return $this;
    }

    /**
     * unserObjects()
     * unserts Objects
     * @access private
     * @return void
     */
    private function unsetObjects() {
        foreach ($this as $property => $value):
            if ($this->$property !== $this->query):
                unset($this->$property);
            endif;
        endforeach;
    }

    /**
     * __toString()
     * return query as a string
     * @access public
     * @return string 
     */
    public function __toString() {
        return $this->query;
    }

}

?>
