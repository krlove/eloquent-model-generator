<?php

namespace Krlove\Generator;

use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Table;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Str;
use Krlove\Generator\Model\BelongsTo;
use Krlove\Generator\Model\BelongsToMany;
use Krlove\Generator\Model\HasMany;
use Krlove\Generator\Model\Model;
use Krlove\Generator\Model\Property;

class Builder
{
    /**
     * @var AbstractSchemaManager
     */
    protected $manager;

    /**
     * Builder constructor.
     * @param DatabaseManager $databaseManager
     */
    public function __construct(DatabaseManager $databaseManager)
    {
        $this->manager = $databaseManager->connection()->getDoctrineSchemaManager();
    }

    /**
     * @param Config $config
     * @return Model
     */
    public function createModel(Config $config)
    {
        $model = new Model($config->get('table_name'));

        $this->setClassName($model, $config);
        $this->setNamespace($model, $config);
        $this->setBaseClass($model, $config);
        $this->setFields($model);
        $this->setRelations($model);

        return $model;
    }

    /**
     * @param Model  $model
     * @param Config $config
     */
    protected function setClassName(Model $model, Config $config)
    {
        $className = $config->get('class_name', Str::studly(Str::singular($config->get('table_name'))));
        $model->setClassName($className);
    }

    /**
     * @param Model  $model
     * @param Config $config
     */
    protected function setNamespace(Model $model, Config $config)
    {
        $namespace = $config->get('namespace');
        $model->setNamespace($namespace);
    }

    /**
     * @param Model  $model
     * @param Config $config
     */
    protected function setBaseClass(Model $model, Config $config)
    {
        $baseClassName = $config->get('base_class_name');
        $model->setBaseClass($baseClassName);
    }

    /**
     * @param Model $model
     */
    protected function setFields(Model $model)
    {
        $tableDetails = $this->manager->listTableDetails($model->getTableName());
        $primaryColumnNames = $tableDetails->getPrimaryKey()->getColumns();

        $columnNames = [];
        foreach ($tableDetails->getColumns() as $column) {
            $property = Property::createVirtualProperty($column->getName(), $column->getType()->getName());
            $model->addProperty($property);

            if (!in_array($column->getName(), $primaryColumnNames)) {
                $columnNames[] = $column->getName();
            }
        }

        $fillable = new Property('fillable', 'protected', 'array', $columnNames);
        $model->addProperty($fillable);
    }

    /**
     * @param Model $model
     */
    protected function setRelations(Model $model)
    {
        $foreignKeys = $this->manager->listTableForeignKeys($model->getTableName());
        foreach ($foreignKeys as $foreignKey) {
            $columns = $foreignKey->getColumns();
            if (count($columns) !== 1) {
                continue;
            }

            $column    = reset($columns);
            $tableName = $foreignKey->getForeignTableName();
            $relation = new BelongsTo($tableName);
            $model->addRelation($relation);
        }

        $tables = $this->manager->listTables();
        foreach ($tables as $table) {
            $foreignKeys = $table->getForeignKeys();
            foreach ($foreignKeys as $name => $foreignKey) {
                if ($foreignKey->getForeignTableName() === $model->getTableName()) {
                    $columns   = $foreignKey->getColumns();
                    if (count($columns) !== 1) {
                        continue;
                    }
                    $column    = reset($columns);

                    $refColumns = $table->getColumns();
                    if (count($refColumns) === 2 && count($foreignKeys) === 2) {
                        $keys = array_keys($foreignKeys);
                        $key = array_search($name, $keys) === 0 ? 1 : 0;
                        $secondForeignKey = $foreignKeys[$keys[$key]];
                        $secondForeignTable = $secondForeignKey->getForeignTableName();

                        $relation = new BelongsToMany($secondForeignTable);
                        $model->addRelation($relation);

                        break;
                    } else {
                        $tableName = $foreignKey->getLocalTableName();

                        $relation = new HasMany($tableName);
                        $model->addRelation($relation);
                    }
                }
            }
        }
    }
}
