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
use Krlove\EloquentModelGenerator\Helper\RelationHelper;

/**
 * Class EloquentModel
 * @package Krlove\EloquentModelGenerator\Model
 */
class EloquentModel extends ClassModel
{
    /**
     * @var string
     */
    protected $tableName;

    /**
     * EloquentModel constructor.
     * @param $tableName
     */
    public function __construct($tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * @param Relation $relation
     * @return $this
     * @throws GeneratorException
     */
    public function addRelation(Relation $relation)
    {
        $relationClass = Str::studly($relation->getTableName());
        if ($relation instanceof HasOne) {
            $name     = Str::camel($relation->getTableName());
            $docBlock = sprintf('@return \%s', EloquentHasOne::class);

            $virtualPropertyType = $relationClass;
        } elseif ($relation instanceof HasMany) {
            $name     = Str::plural(Str::camel($relation->getTableName()));
            $docBlock = sprintf('@return \%s', EloquentHasMany::class);

            $virtualPropertyType = sprintf('%s[]', $relationClass);
        } elseif ($relation instanceof BelongsTo) {
            $name     = Str::camel($relation->getTableName());
            $docBlock = sprintf('@return \%s', EloquentBelongsTo::class);

            $virtualPropertyType = $relationClass;
        } elseif ($relation instanceof BelongsToMany) {
            $name     = Str::plural(Str::camel($relation->getTableName()));
            $docBlock = sprintf('@return \%s', EloquentBelongsToMany::class);

            $virtualPropertyType = sprintf('%s[]', $relationClass);
        } else {
            throw new GeneratorException('Relation not supported');
        }

        $method = new MethodModel($name);
        $method->setBody($this->createMethodBody($relation));
        $method->setDocBlock(new DocBlockModel($docBlock));

        $this->addMethod($method);
        $this->addProperty(new VirtualPropertyModel($name, $virtualPropertyType));

        return $this;
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @param Relation $relation
     * @return string
     * @throws GeneratorException
     */
    protected function createMethodBody(Relation $relation)
    {
        $reflectionObject = new \ReflectionObject($relation);
        $name = Str::camel($reflectionObject->getShortName());

        $arguments = [Str::studly($relation->getTableName())];
        if ($relation instanceof BelongsToMany) {
            $defaultJoinTableName = RelationHelper::getDefaultJoinTableName($this->tableName, $relation->getTableName());
            $joinTableName        = $relation->getJoinTable() === $defaultJoinTableName ? null : $relation->getJoinTable();
            $arguments[]          = $joinTableName;

            $arguments[] = $this->resolveArgument(
                $relation->getForeignColumnName(),
                RelationHelper::getDefaultForeignColumnName($this->getTableName())
            );
            $arguments[] = $this->resolveArgument(
                $relation->getLocalColumnName(),
                RelationHelper::getDefaultForeignColumnName($relation->getTableName())
            );
        } elseif ($relation instanceof HasMany) {
            $arguments[] = $this->resolveArgument(
                $relation->getForeignColumnName(),
                RelationHelper::getDefaultForeignColumnName($this->getTableName())
            );
            $arguments[] = $this->resolveArgument(
                $relation->getLocalColumnName(),
                RelationHelper::$defaultPrimaryKey
            );
        } else {
            $arguments[] = $this->resolveArgument(
                $relation->getForeignColumnName(),
                RelationHelper::getDefaultForeignColumnName($relation->getTableName())
            );
            $arguments[] = $this->resolveArgument(
                $relation->getLocalColumnName(),
                RelationHelper::$defaultPrimaryKey
            );
        }

        return sprintf('return $this->%s(%s);', $name, $this->prepareArguments($arguments));
    }

    /**
     * @param array $array
     * @return array
     */
    protected function prepareArguments(array $array)
    {
        $array     = array_reverse($array);
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

    /**
     * @param string $actual
     * @param string $default
     * @return string|null
     */
    protected function resolveArgument($actual, $default)
    {
        return $actual === $default ? null : $actual;
    }
}
// public function hasOne($related, $foreignKey = null, $localKey = null)

// public function hasMany($related, $foreignKey = null, $localKey = null)

// public function belongsTo($related, $foreignKey = null, $otherKey = null, $relation = null)

// public function belongsToMany($related, $table = null, $foreignKey = null, $otherKey = null, $relation = null)
// return $this->belongsToMany('App\Role', 'user_roles', 'user_id', 'role_id');
