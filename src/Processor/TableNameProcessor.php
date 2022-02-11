<?php

namespace Krlove\EloquentModelGenerator\Processor;

use Krlove\CodeGenerator\Model\ClassNameModel;
use Krlove\CodeGenerator\Model\DocBlockModel;
use Krlove\CodeGenerator\Model\PropertyModel;
use Krlove\CodeGenerator\Model\UseClassModel;
use Krlove\EloquentModelGenerator\Config;
use Krlove\EloquentModelGenerator\Helper\EmgHelper;
use Krlove\EloquentModelGenerator\Model\EloquentModel;

class TableNameProcessor implements ProcessorInterface
{
    public function __construct(private EmgHelper $helper) {}

    public function process(EloquentModel $model, Config $config): void
    {
        $className = $config->get('class_name');
        $baseClassName = $config->get('base_class_name');
        $tableName = $config->get('table_name');

        $model
            ->setName(new ClassNameModel($className, $this->helper->getShortClassName($baseClassName)))
            ->addUses(new UseClassModel(ltrim($baseClassName, '\\')))
            ->setTableName($tableName ?: $this->helper->getDefaultTableName($className));

        if ($model->getTableName() !== $this->helper->getDefaultTableName($className)) {
            $property = new PropertyModel('table', 'protected', $model->getTableName());
            $property->setDocBlock(new DocBlockModel('The table associated with the model.', '', '@var string'));
            $model->addProperty($property);
        }
    }

    public function getPriority(): int
    {
        return 10;
    }
}
