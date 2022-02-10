<?php

namespace Krlove\EloquentModelGenerator\Model;

use Krlove\CodeGenerator\Model\ClassModel;

class EloquentModel extends ClassModel
{
    protected string $tableName;

    public function setTableName(string $tableName): self
    {
        $this->tableName = $tableName;

        return $this;
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }
}
