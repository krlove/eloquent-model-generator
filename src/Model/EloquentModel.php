<?php

namespace Krlove\EloquentModelGenerator\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo as EloquentBelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany as EloquentBelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany as EloquentHasMany;
use Illuminate\Database\Eloquent\Relations\HasOne as EloquentHasOne;
use Illuminate\Support\Str;
use Krlove\CodeGenerator\Model\ClassModel;
use Krlove\CodeGenerator\Model\DocBlockModel;
use Krlove\CodeGenerator\Model\MethodModel;
use Krlove\CodeGenerator\Model\VirtualPropertyModel;
use Krlove\EloquentModelGenerator\Exception\GeneratorException;
use Krlove\EloquentModelGenerator\Helper\EmgHelper;

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

    public function addRelation(Relation $relation): void
    {
        $relationClass = EmgHelper::getClassNameByTableName($relation->getTableName());
        if ($relation instanceof HasOne) {
            $name = Str::singular(Str::camel($relation->getTableName()));
            $docBlock = sprintf('@return \%s', EloquentHasOne::class);

            $virtualPropertyType = $relationClass;
        } elseif ($relation instanceof HasMany) {
            $name = Str::plural(Str::camel($relation->getTableName()));
            $docBlock = sprintf('@return \%s', EloquentHasMany::class);

            $virtualPropertyType = sprintf('%s[]', $relationClass);
        } elseif ($relation instanceof BelongsTo) {
            $name = Str::singular(Str::camel($relation->getTableName()));
            $docBlock = sprintf('@return \%s', EloquentBelongsTo::class);

            $virtualPropertyType = $relationClass;
        } elseif ($relation instanceof BelongsToMany) {
            $name = Str::plural(Str::camel($relation->getTableName()));
            $docBlock = sprintf('@return \%s', EloquentBelongsToMany::class);

            $virtualPropertyType = sprintf('%s[]', $relationClass);
        } else {
            throw new GeneratorException('Relation not supported');
        }

        $method = new MethodModel($name);
        $method->setBody($this->createRelationMethodBody($relation));
        $method->setDocBlock(new DocBlockModel($docBlock));

        $this->addMethod($method);
        $this->addProperty(new VirtualPropertyModel($name, $virtualPropertyType));
    }

    protected function createRelationMethodBody(Relation $relation): string
    {
        $reflectionObject = new \ReflectionObject($relation);
        $name = Str::camel($reflectionObject->getShortName());

        $arguments = [
            $this->getNamespace()->getNamespace() . '\\' . EmgHelper::getClassNameByTableName($relation->getTableName())
        ];

        if ($relation instanceof BelongsToMany) {
            $defaultJoinTableName = EmgHelper::getDefaultJoinTableName(
                $this->getTableName(),
                $relation->getTableName()
            );
            $joinTableName = $relation->getJoinTable() === $defaultJoinTableName
                ? null
                : $relation->getJoinTable();
            $arguments[] = $joinTableName;

            $arguments[] = $this->resolveArgument(
                $relation->getForeignColumnName(),
                EmgHelper::getDefaultForeignColumnName($this->getTableName())
            );
            $arguments[] = $this->resolveArgument(
                $relation->getLocalColumnName(),
                EmgHelper::getDefaultForeignColumnName($relation->getTableName())
            );
        } elseif ($relation instanceof HasMany) {
            $arguments[] = $this->resolveArgument(
                $relation->getForeignColumnName(),
                EmgHelper::getDefaultForeignColumnName($this->getTableName())
            );
            $arguments[] = $this->resolveArgument(
                $relation->getLocalColumnName(),
                EmgHelper::DEFAULT_PRIMARY_KEY
            );
        } else {
            $arguments[] = $this->resolveArgument(
                $relation->getForeignColumnName(),
                EmgHelper::getDefaultForeignColumnName($relation->getTableName())
            );
            $arguments[] = $this->resolveArgument(
                $relation->getLocalColumnName(),
                EmgHelper::DEFAULT_PRIMARY_KEY
            );
        }

        return sprintf('return $this->%s(%s);', $name, $this->createRelationMethodArguments($arguments));
    }

    protected function createRelationMethodArguments(array $array): string
    {
        $array = array_reverse($array);
        $milestone = false;
        foreach ($array as $key => &$item) {
            if (!$milestone) {
                if (!is_string($item)) {
                    unset($array[$key]);
                } else {
                    $milestone = true;
                }
            } else {
                if ($item === null) {
                    $item = 'null';

                    continue;
                }
            }
            $item = sprintf("'%s'", $item);
        }

        return implode(', ', array_reverse($array));
    }

    protected function resolveArgument(string $actual, string $default): ?string
    {
        return $actual === $default ? null : $actual;
    }
}
