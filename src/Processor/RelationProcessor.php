<?php

namespace Krlove\EloquentModelGenerator\Processor;

use Doctrine\DBAL\Schema\Table;
use Illuminate\Database\DatabaseManager;
use Krlove\EloquentModelGenerator\Config\Config;
use Krlove\EloquentModelGenerator\Helper\Prefix;
use Krlove\EloquentModelGenerator\Model\BelongsTo;
use Krlove\EloquentModelGenerator\Model\BelongsToMany;
use Krlove\EloquentModelGenerator\Model\EloquentModel;
use Krlove\EloquentModelGenerator\Model\HasMany;
use Krlove\EloquentModelGenerator\Model\HasOne;

class RelationProcessor implements ProcessorInterface
{
    public function __construct(private DatabaseManager $databaseManager) {}

    public function process(EloquentModel $model, Config $config): void
    {
        $schemaManager = $this->databaseManager->connection($config->getConnection())->getDoctrineSchemaManager();
        $prefix = $this->databaseManager->connection($config->getConnection())->getTablePrefix();
        
        $prefixedTableName = Prefix::add($prefix, $model->getTableName());
        $foreignKeys = $schemaManager->listTableForeignKeys($prefixedTableName);
        foreach ($foreignKeys as $tableForeignKey) {
            $tableForeignColumns = $tableForeignKey->getForeignColumns();
            if (count($tableForeignColumns) !== 1) {
                continue;
            }

            $relation = new BelongsTo(
                Prefix::remove($prefix, $tableForeignKey->getForeignTableName()),
                $tableForeignKey->getLocalColumns()[0],
                $tableForeignColumns[0]
            );
            $model->addRelation($relation);
        }

        $tables = $schemaManager->listTables();
        foreach ($tables as $table) {
            if ($table->getName() === $prefixedTableName) {
                continue;
            }

            $foreignKeys = $schemaManager->listTableForeignKeys($table->getName());
            foreach ($foreignKeys as $name => $foreignKey) {
                if ($foreignKey->getForeignTableName() === $prefixedTableName) {
                    $localColumns = $foreignKey->getLocalColumns();
                    if (count($localColumns) !== 1) {
                        continue;
                    }

                    if (count($foreignKeys) === 2 && count($table->getColumns()) === 2) {
                        $keys = array_keys($foreignKeys);
                        $key = array_search($name, $keys) === 0 ? 1 : 0;
                        $secondForeignKey = $foreignKeys[$keys[$key]];
                        $secondForeignTable = Prefix::remove($prefix, $secondForeignKey->getForeignTableName());

                        $relation = new BelongsToMany(
                            $secondForeignTable,
                            Prefix::remove($prefix, $table->getName()),
                            $localColumns[0],
                            $secondForeignKey->getLocalColumns()[0]
                        );
                        $model->addRelation($relation);

                        break;
                    } else {
                        $tableName = Prefix::remove($prefix, $table->getName());
                        $foreignColumn = $localColumns[0];
                        $localColumn = $foreignKey->getForeignColumns()[0];

                        if ($this->isColumnUnique($table, $foreignColumn)) {
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

    protected function isColumnUnique(Table $table, string $column): bool
    {
        foreach ($table->getIndexes() as $index) {
            $indexColumns = $index->getColumns();
            if (count($indexColumns) !== 1) {
                continue;
            }
            $indexColumn = $indexColumns[0];
            if ($indexColumn === $column && $index->isUnique()) {
                return true;
            }
        }

        return false;
    }
}
