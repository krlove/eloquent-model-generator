<?php

namespace Krlove\EloquentModelGenerator\Model;

/**
 * Class Relation
 * @package Krlove\EloquentModelGenerator\Model
 */
abstract class Relation
{
    /**
     * @var string
     */
    public $defaultPrimaryKey = 'id';

    /**
     * @var string
     */
    protected $table;

    /**
     * @var string
     */
    protected $foreignColumnName;

    /**
     * @var string
     */
    protected $localColumnName;

    /**
     * @return string
     */
    public function getDefaultForeignColumnName()
    {
        return sprintf('%s_%s', $this->table, $this->defaultPrimaryKey);
    }

    /**
     * Relation constructor.
     * @param string $table
     * @param string $foreignColumnName
     * @param string $localColumnName
     */
    public function __construct($table, $foreignColumnName, $localColumnName)
    {
        $this->setTable($table);
        $this->setForeignColumnName($foreignColumnName);
        $this->setLocalColumnName($localColumnName);
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

    /**
     * @return string
     */
    public function getForeignColumnName()
    {
        return $this->foreignColumnName;
    }

    /**
     * @param string $foreignColumnName
     *
     * @return $this
     */
    public function setForeignColumnName($foreignColumnName)
    {
        $this->foreignColumnName = $foreignColumnName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLocalColumnName()
    {
        return $this->localColumnName;
    }

    /**
     * @param string $localColumnName
     *
     * @return $this
     */
    public function setLocalColumnName($localColumnName)
    {
        $this->localColumnName = $localColumnName;

        return $this;
    }
}
