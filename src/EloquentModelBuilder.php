<?php

namespace Krlove\Generator;

use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Str;
use Krlove\Generator\Model\ClassNameModel;
use Krlove\Generator\Model\EloquentModel;
use Krlove\Generator\Model\NamespaceModel;
use Krlove\Generator\Model\PropertyModel;

class EloquentModelBuilder
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
     * @return EloquentModel
     */
    public function createModel(Config $config)
    {
        $model = new EloquentModel();

        $this->setClassName($model, $config)
            ->setNamespace($model, $config)
            ->setFields($model, $config);

        return $model;
    }

    /**
     * @param EloquentModel $model
     * @param Config $config
     * @return $this
     */
    protected function setClassName(EloquentModel $model, Config $config)
    {
        $className = $config->get('class_name', Str::studly(Str::singular($config->get('table_name'))));
        $model->setName(new ClassNameModel($className, $config->get('base_class_name')));

        return $this;
    }

    /**
     * @param EloquentModel $model
     * @param Config $config
     * @return $this
     */
    protected function setNamespace(EloquentModel $model, Config $config)
    {
        $namespace = $config->get('namespace');
        $model->setNamespace(new NamespaceModel($namespace));

        return $this;
    }

    /**
     * @param EloquentModel $model
     * @param Config $config
     * @return $this
     */
    protected function setFields(EloquentModel $model, Config $config)
    {
        $tableDetails = $this->manager->listTableDetails($config->get('table_name'));
        $primaryColumnNames = $tableDetails->getPrimaryKey()->getColumns();

        $columnNames = [];
        foreach ($tableDetails->getColumns() as $column) {
            // todo add virtual properties

            if (!in_array($column->getName(), $primaryColumnNames)) {
                $columnNames[] = $column->getName();
            }
        }

        $fillableProperty = new PropertyModel('fillable');
        $fillableProperty->setModifier('protected')
            ->setValue($columnNames);
        $model->addProperty($fillableProperty);

        return $this;
    }

    /**
     * @param EloquentModel $model
     */
    protected function setRelations(Model $model, Config $config)
    {
        $foreignKeys = $this->manager->listTableForeignKeys($model->getTableName());
        foreach ($foreignKeys as $foreignKey) {
            $columns = $foreignKey->getColumns();
            if (count($columns) !== 1) {
                continue;
            }

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
