<?php

namespace Krlove\EloquentModelGenerator\Model;

abstract class Relation
{
    protected string $tableName;
    protected string $foreignColumnName;
    protected string $localColumnName;

    public function __construct(string $tableName, string $joinColumnName, string $localColumnName)
    {
        $this->setTableName($tableName);
        $this->setForeignColumnName($joinColumnName);
        $this->setLocalColumnName($localColumnName);
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function setTableName(string $tableName): self
    {
        $this->tableName = $tableName;

        return $this;
    }

    public function getForeignColumnName(): string
    {
        return $this->foreignColumnName;
    }

    public function setForeignColumnName(string $foreignColumnName): self
    {
        $this->foreignColumnName = $foreignColumnName;

        return $this;
    }

    public function getLocalColumnName(): string
    {
        return $this->localColumnName;
    }

    public function setLocalColumnName(string $localColumnName): self
    {
        $this->localColumnName = $localColumnName;

        return $this;
    }
}
