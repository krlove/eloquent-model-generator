<?php

namespace Krlove\Generator\Model;

/**
 * Class Relation
 * @package Krlove\Generator\Model
 */
abstract class Relation
{
    /**
     * @var string
     */
    protected $table;

    /**
     * TODO: add support for custom keys
     * Relation constructor.
     * @param string $table
     */
    public function __construct($table)
    {
        $this->setTable($table);
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
