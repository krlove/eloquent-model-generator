<?php

namespace Krlove\EloquentModelGenerator\Model;

class BelongsToMany extends Relation
{
    protected string $joinTable;

    public function __construct(string $tableName, string $joinTable, string $foreignColumnName, string $localColumnName)
    {
        $this->joinTable = $joinTable;
        parent::__construct($tableName, $foreignColumnName, $localColumnName);
    }

    public function getJoinTable(): string
    {
        return $this->joinTable;
    }
}
