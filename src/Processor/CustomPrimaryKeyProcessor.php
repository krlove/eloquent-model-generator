<?php

namespace Krlove\EloquentModelGenerator\Processor;

use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\BlueprintState;
use Krlove\CodeGenerator\Model\DocBlockModel;
use Krlove\CodeGenerator\Model\PropertyModel;
use Krlove\EloquentModelGenerator\Config\Config;
use Krlove\EloquentModelGenerator\Helper\Prefix;
use Krlove\EloquentModelGenerator\Model\EloquentModel;
use Krlove\EloquentModelGenerator\TypeRegistry;

class CustomPrimaryKeyProcessor implements ProcessorInterface
{
    public function __construct(
        private DatabaseManager $databaseManager,
        private TypeRegistry    $typeRegistry,
    ) {
    }

    public function process(EloquentModel $model, Config $config): void
    {
        $connection    = $this->databaseManager->connection($config->getConnection());
        $schemaGrammar = $connection->getSchemaGrammar();

        $tableName      = Prefix::add($model->getTableName());
        $blueprint      = new Blueprint($connection, $tableName);
        $blueprintState = new BlueprintState($blueprint, $connection, $schemaGrammar);

        $primaryKey = $blueprintState->getPrimaryKey();
        if ($primaryKey === null) {
            return;
        }

        $primaryKeyColumns = $primaryKey->columns;
        if (count($primaryKeyColumns) !== 1) {
            return;
        }

        $columns    = $blueprintState->getColumns();
        $column     = collect($columns)->filter(fn ($column) => $column->name === $primaryKeyColumns[0])->first();
        $columnName = $column->name;

        if ($columnName !== 'id') {
            $primaryKeyProperty = new PropertyModel('primaryKey', 'protected', $columnName);
            $primaryKeyProperty->setDocBlock(
                new DocBlockModel('The primary key for the model.', '', '@var string')
            );
            $model->addProperty($primaryKeyProperty);
        }

        $columnTypeName = $column->type;
        if ($columnTypeName !== 'integer') {
            $keyType = $this->typeRegistry->resolveType($columnTypeName);

            $keyTypeProperty = new PropertyModel(
                'keyType',
                'protected',
                $keyType,
            );
            $keyTypeProperty->setDocBlock(
                new DocBlockModel('The "type" of the auto-incrementing ID.', '', '@var string')
            );
            $model->addProperty($keyTypeProperty);
        }

        if (! $column->autoincrement()) {
            $autoincrementProperty = new PropertyModel('incrementing', 'public', false);
            $autoincrementProperty->setDocBlock(
                new DocBlockModel('Indicates if the IDs are auto-incrementing.', '', '@var bool')
            );
            $model->addProperty($autoincrementProperty);
        }
    }

    public function getPriority(): int
    {
        return 6;
    }
}
