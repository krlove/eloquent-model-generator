<?php

namespace Krlove\Generator\Model;

/**
 * Class Relation
 * @package Krlove\Generator\Model
 */
class Relation
{
    /**
     * @var string
     */
    protected $column;

    /**
     * @var string
     */
    protected $table;

    /**
     * Relation constructor.
     * @param string $column
     * @param string $table
     */
    public function __construct($column, $table)
    {
        $this->setColumn($column);
        $this->setTable($table);
    }

    /**
     * @return string
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function setColumn($column)
    {
        $this->column = $column;

        return $this;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param string $table
     *
     * @return $this
     */
    public function setTable($table)
    {
        $this->table = $table;

        return $this;
    }
}
