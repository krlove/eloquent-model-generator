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
    protected $tableName;

    /**
     * @var string
     */
    protected $foreignColumnName;

    /**
     * @var string
     */
    protected $localColumnName;

    /**
     * Relation constructor.
     * @param string $tableName
     * @param string $joinColumnName
     * @param string $localColumnName
     */
    public function __construct($tableName, $joinColumnName, $localColumnName)
    {
        $this->setTableName($tableName);
        $this->setForeignColumnName($joinColumnName);
        $this->setLocalColumnName($localColumnName);
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @param string $tableName
     *
     * @return $this
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;

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
