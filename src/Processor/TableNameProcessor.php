<?php

namespace Krlove\EloquentModelGenerator\Processor;

use Illuminate\Database\DatabaseManager;
use Krlove\CodeGenerator\Model\ClassNameModel;
use Krlove\CodeGenerator\Model\DocBlockModel;
use Krlove\CodeGenerator\Model\PropertyModel;
use Krlove\CodeGenerator\Model\UseClassModel;
use Krlove\EloquentModelGenerator\Config\Config;
use Krlove\EloquentModelGenerator\Exception\GeneratorException;
use Krlove\EloquentModelGenerator\Helper\EmgHelper;
use Krlove\EloquentModelGenerator\Helper\Prefix;
use Krlove\EloquentModelGenerator\Model\EloquentModel;

class TableNameProcessor implements ProcessorInterface
{
    public function __construct(private DatabaseManager $databaseManager) {}

    public function process(EloquentModel $model, Config $config): void
    {
        $className = $config->getClassName();
        $baseClassName = $config->getBaseClassName();
        $tableName = $config->getTableName() ?: EmgHelper::getTableNameByClassName($className);

        $schemaManager = $this->databaseManager->connection($config->getConnection())->getDoctrineSchemaManager();
        $prefixedTableName = Prefix::add($tableName);
        if (!$schemaManager->tablesExist($prefixedTableName)) {
            throw new GeneratorException(sprintf('Table %s does not exist', $prefixedTableName));
        }

        $model
            ->setName(new ClassNameModel($className, EmgHelper::getShortClassName($baseClassName)))
            ->addUses(new UseClassModel(ltrim($baseClassName, '\\')))
            ->setTableName($tableName);

        if ($model->getTableName() !== EmgHelper::getTableNameByClassName($className)) {
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
