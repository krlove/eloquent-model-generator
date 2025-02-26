<?php

namespace Krlove\EloquentModelGenerator\Processor;

use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\BlueprintState;
use Krlove\CodeGenerator\Model\DocBlockModel;
use Krlove\CodeGenerator\Model\PropertyModel;
use Krlove\CodeGenerator\Model\VirtualPropertyModel;
use Krlove\EloquentModelGenerator\Config\Config;
use Krlove\EloquentModelGenerator\Helper\Prefix;
use Krlove\EloquentModelGenerator\Model\EloquentModel;
use Krlove\EloquentModelGenerator\TypeRegistry;

class FieldProcessor implements ProcessorInterface
{
    public function __construct(
        private DatabaseManager $databaseManager,
        private TypeRegistry $typeRegistry,
    ) {
    }

    public function process(EloquentModel $model, Config $config): void
    {
        $connection    = $this->databaseManager->connection($config->getConnection());
        $schemaGrammar = $connection->getSchemaGrammar();

        $tableName      = Prefix::add($model->getTableName());
        $blueprint      = new Blueprint($connection, $tableName);
        $blueprintState = new BlueprintState($blueprint, $connection, $schemaGrammar);
        $columns        = $blueprintState->getColumns();

        $primaryKey         = $blueprintState->getPrimaryKey();
        $primaryColumnNames = $primaryKey->columns;

        $columnNames = [];

        foreach ($columns as $column) {
            $model->addProperty(new VirtualPropertyModel(
                $column->name,
                $this->typeRegistry->resolveType($column->type)
            ));

            if (! in_array($column->name, $primaryColumnNames)) {
                $columnNames[] = $column->name;
            }
        }

        $fillableProperty = new PropertyModel('fillable');
        $fillableProperty->setAccess('protected')
            ->setValue($columnNames)
            ->setDocBlock(new DocBlockModel('@var array'));
        $model->addProperty($fillableProperty);
    }

    public function getPriority(): int
    {
        return 5;
    }
}
