<?php

namespace Krlove\Generator;

use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Str;
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

        $model->addProperty(new Property('someProperty', 'protected', null, 'some value'));
        $model->addProperty(new Property('someProperty2', 'private', null, 'some value 2'));

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
        $columns = $this->manager->listTableColumns($model->getTableName());
        foreach ($columns as $column) {
            $property = Property::createVirtualProperty($column->getName(), $column->getType()->getName());
            $model->addProperty($property);
        }
    }

    /**
     * @param Model $model
     */
    protected function setRelations(Model $model)
    {
        // todo set relations
    }
}
