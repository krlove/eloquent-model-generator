<?php

namespace Krlove\EloquentModelGenerator\Processor;

use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\BlueprintState;
use Krlove\EloquentModelGenerator\Config\Config;
use Krlove\EloquentModelGenerator\Helper\EmgHelper;
use Krlove\EloquentModelGenerator\Helper\Prefix;
use Krlove\EloquentModelGenerator\Model\BelongsTo;
use Krlove\EloquentModelGenerator\Model\BelongsToMany;
use Krlove\EloquentModelGenerator\Model\EloquentModel;
use Krlove\EloquentModelGenerator\Model\HasMany;
use Krlove\EloquentModelGenerator\Model\HasOne;

class RelationProcessor implements ProcessorInterface
{
    public function __construct(
        private DatabaseManager $databaseManager,
    ) {
    }

    public function process(EloquentModel $model, Config $config): void
    {
        $connection    = $this->databaseManager->connection($config->getConnection());
        $schemaBuilder = $connection->getSchemaBuilder();
        $schemaGrammar = $connection->getSchemaGrammar();

        $prefixedTableName = Prefix::add($model->getTableName());
        $tables            = $schemaBuilder->getTableListing(null, false);

        foreach ($tables as $table) {
            $tableName      = $table;
            $blueprint      = new Blueprint($connection, $tableName);
            $blueprintState = new BlueprintState($blueprint, $connection, $schemaGrammar);
            $columns        = $blueprintState->getColumns();
            $indexes        = $schemaBuilder->getIndexes($tableName);

            $foreignKeys = $schemaBuilder->getForeignKeys($tableName);
            foreach ($foreignKeys as $index => $foreignKey) {
                $name         = $foreignKey['name'];
                $localColumns = $foreignKey['columns'];

                if (count($localColumns) !== 1) {
                    continue;
                }

                $foreignTableName    = $foreignKey['foreign_table'];
                $foreignTableColumns = $foreignKey['foreign_columns'];

                if ($tableName === $prefixedTableName) {
                    $relation = new BelongsTo(
                        Prefix::remove($foreignTableName),
                        $localColumns[0],
                        $foreignTableColumns[0]
                    );
                    $model->addRelation($relation);
                } elseif ($foreignTableName === $prefixedTableName) {
                    if (count($foreignKeys) === 2 && count($columns) === 2) {
                        $keyIndex           = 1 - $index;
                        $secondForeignKey   = $foreignKeys[$keyIndex];
                        $secondForeignTable = Prefix::remove($secondForeignKey['foreign_table']);

                        $relation = new BelongsToMany(
                            $secondForeignTable,
                            Prefix::remove($tableName),
                            $localColumns[0],
                            $secondForeignKey['columns'][0]
                        );
                        $model->addRelation($relation);

                        break;
                    } else {
                        $tableName     = Prefix::remove($tableName);
                        $foreignColumn = $localColumns[0];
                        $localColumn   = $foreignTableColumns[0];

                        if (EmgHelper::isColumnUniqueIndex($indexes, $foreignColumn)) {
                            $relation = new HasOne($tableName, $foreignColumn, $localColumn);
                        } else {
                            $relation = new HasMany($tableName, $foreignColumn, $localColumn);
                        }

                        $model->addRelation($relation);
                    }
                }
            }
        }
    }

    public function getPriority(): int
    {
        return 5;
    }
}
