<?php

namespace Krlove\EloquentModelGenerator;

use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Str;
use Krlove\EloquentModelGenerator\Model\BelongsTo;
use Krlove\EloquentModelGenerator\Model\EloquentModel;
use Krlove\CodeGenerator\Model\ClassNameModel;
use Krlove\CodeGenerator\Model\NamespaceModel;
use Krlove\CodeGenerator\Model\PropertyModel;
use Krlove\CodeGenerator\Model\VirtualPropertyModel;

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
            ->setFields($model, $config)
            ->setRelations($model, $config);

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
            // TODO: db type to php type
            $virtualProperty = new VirtualPropertyModel($column->getName());
            $model->addProperty($virtualProperty);

            if (!in_array($column->getName(), $primaryColumnNames)) {
                $columnNames[] = $column->getName();
            }
        }

        $fillableProperty = new PropertyModel('fillable');
        $fillableProperty->setAccess('protected')
            ->setValue($columnNames);
        $model->addProperty($fillableProperty);

        return $this;
    }

    /**
     * @param EloquentModel $model
     * @param Config $config
     */
    protected function setRelations(EloquentModel $model, Config $config)
    {
        $foreignKeys = $this->manager->listTableForeignKeys($config->get('table_name'));
        foreach ($foreignKeys as $foreignKey) {
            $foreignColumns = $foreignKey->getForeignColumns();
            if (count($foreignColumns) !== 1) {
                continue;
            }

            // TODO: check if unique
            $relation = new BelongsTo(
                $foreignKey->getForeignTableName(),
                $foreignColumns[0],
                $foreignKey->getLocalColumns()[0]
            );
            $model->addRelation($relation);
        }

        $tables = $this->manager->listTables();
        foreach ($tables as $table) {
            $foreignKeys = $table->getForeignKeys();
            foreach ($foreignKeys as $name => $foreignKey) {
                if ($foreignKey->getForeignTableName() === $config->get('table_name')) {
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
