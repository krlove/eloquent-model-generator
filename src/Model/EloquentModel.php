<?php

namespace Krlove\EloquentModelGenerator\Model;

use Krlove\CodeGenerator\Model\ClassModel;

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

    protected $addClassPhpDocBlock = TRUE;

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
    public function getTableName()
    {
        return $this->tableName;
    }

    public function setAddClassPhpDocBlock(bool $addClassPhpDocBlock){
        $this->addClassPhpDocBlock = $addClassPhpDocBlock;
    }

    /**
     * Convert virtual properties and methods to DocBlock content
     */
    protected function prepareDocBlock()
    {
        if($this->addClassPhpDocBlock === FALSE){
            parent::prepareDocBlock();
        }
    }
}
