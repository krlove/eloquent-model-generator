<?php

namespace Krlove\EloquentModelGenerator\Model;

use Illuminate\Support\Str;
use Krlove\CodeGenerator\Model\ArgumentModel;
use Krlove\CodeGenerator\Model\ClassModel;
use Krlove\CodeGenerator\Model\MethodModel;

/**
 * Class EloquentModel
 * @package Krlove\EloquentModelGenerator\Model
 */
class EloquentModel extends ClassModel
{
    /**
     * @param Relation $relation
     * @return $this
     */
    public function addRelation(Relation $relation)
    {
        $relationClass = Str::studly($relation->getTable());
        if ($relation instanceof HasOne) {
            $method = new MethodModel($relation->getTable());
            $method->addArgument(new ArgumentModel($relationClass));
        }

        if (isset($method)) {
            if ($relation->getForeignColumnName() !== $relation->getDefaultForeignColumnName()) {
                $method->addArgument(new ArgumentModel($relation->getForeignColumnName()));
            }
            if ($relation->getLocalColumnName() !== $relation->defaultPrimaryKey) {
                $method->addArgument(new ArgumentModel($relation->getLocalColumnName()));
            }
        }

        return $this;
    }
}
