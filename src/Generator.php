<?php

namespace Krlove\Generator;

use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Str;
use Krlove\Generator\Model\Model;
use Krlove\Generator\Model\Property;

/**
 * Class Generator
 * @package Krlove\Generator
 */
class Generator
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var Renderer
     */
    protected $renderer;

    /**
     * @var DatabaseManager
     */
    protected $databaseManager;

    /**
     * Generator constructor.
     * @param DatabaseManager $databaseManager
     * @param Renderer $renderer
     */
    public function __construct(DatabaseManager $databaseManager, Renderer $renderer)
    {
        $this->databaseManager = $databaseManager;
        $this->renderer        = $renderer;
    }

    /**
     * @param array $config
     * @return string
     * @throws RendererException
     * @throws \Exception
     */
    public function generateModel(array $config)
    {
        $this->setConfig($config);
        $model = $this->createModel();

        return $this->renderer->render($model);
    }

    /**
     * @return Model
     */
    protected function createModel()
    {
        $model = new Model($this->get('table_name'));

        $className = $this->has('class_name')
            ? $this->get('class_name')
            : Str::studly(Str::singular($this->get('table_name')));
        $model->setClassName($className);

        $namespace = $this->has('namespace')
            ? $this->get('namespace')
            : 'App';
        $model->setNamespace($namespace);

        $baseClassName = $this->has('base-class')
            ? $this->get('base-class')
            : \Illuminate\Database\Eloquent\Model::class;
        $model->setBaseClass($baseClassName);

        $manager = $this->databaseManager->connection()->getDoctrineSchemaManager();
        $columns = $manager->listTableColumns($model->getTableName());
        foreach ($columns as $column) {
            $property = Property::createVirtualProperty($column->getName(), $column->getType()->getName());
            $model->addProperty($property);
        }

        $model->addProperty(new Property('someProperty', 'protected', null, 'some value'));
        $model->addProperty(new Property('someProperty2', 'private', null, 'some value 2'));

        return $model;
    }

    /**
     * @param array $config
     * @throws \Exception
     */
    protected function setConfig(array $config)
    {
        if (!isset($config['table_name'])) {
            throw new \Exception('`table_name` is not defined');
        }

        $this->config = $config;
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    protected function get($key)
    {
        return $this->has($key) ? $this->config[$key] : null;
    }

    /**
     * @param string $key
     * @return bool
     */
    protected function has($key)
    {
        return array_key_exists($key, $this->config);
    }
}
