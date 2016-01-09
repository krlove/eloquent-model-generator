<?php

namespace Krlove\Generator\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Class Model
 * @package Krlove\Generator\Model
 */
class Model
{
    /**
     * @var string
     */
    protected $className;

    /**
     * @var string
     */
    protected $namespace = 'App';

    /**
     * @var string
     */
    protected $baseClass = 'Illuminate\Database\Eloquent\Model';

    /**
     * @var Collection|Property[]
     */
    protected $properties;

    /**
     * @var Collection|Relation[]
     */
    protected $relations;

    /**
     * @var string
     */
    protected $tableName;

    /**
     * Model constructor.
     * @param string $tableName
     */
    public function __construct($tableName)
    {
        $this->tableName  = $tableName;
        $this->properties = new ArrayCollection();
        $this->relations  = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @param string $className
     *
     * @return $this
     */
    public function setClassName($className)
    {
        $this->className = $className;

        return $this;
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @param string $namespace
     *
     * @return $this
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;

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
     * @return string
     */
    public function getBaseClass()
    {
        return $this->baseClass;
    }

    /**
     * @param string $baseClass
     *
     * @return $this
     */
    public function setBaseClass($baseClass)
    {
        $this->baseClass = $baseClass;

        return $this;
    }

    /**
     * @return Collection|Property[]
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param Property $property
     * @return $this
     */
    public function addProperty(Property $property)
    {
        if (!$this->properties->contains($property)) {
            $this->properties->add($property);
        }

        return $this;
    }

    /**
     * @return Collection|Relation[]
     */
    public function getRelations()
    {
        return $this->relations;
    }

    /**
     * @param Relation $relation
     * @return $this
     */
    public function addRelation(Relation $relation)
    {
        if (!$this->relations->contains($relation)) {
            $this->relations->add($relation);
        }

        return $this;
    }
}
