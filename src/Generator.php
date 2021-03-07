<?php

namespace Krlove\EloquentModelGenerator;

use Krlove\EloquentModelGenerator\Exception\GeneratorException;
use Krlove\CodeGenerator\Model\ClassModel;

class Generator
{
    /**
     * @var EloquentModelBuilder
     */
    protected $builder;

    /**
     * @var TypeRegistry
     */
    protected $typeRegistry;

    /**
     * @param EloquentModelBuilder $builder
     * @param TypeRegistry $typeRegistry
     */
    public function __construct(EloquentModelBuilder $builder, TypeRegistry $typeRegistry)
    {
        $this->builder = $builder;
        $this->typeRegistry = $typeRegistry;
    }

    /**
     * @param Config $config
     * @return ClassModel
     * @throws GeneratorException
     */
    public function generateModel(Config $config)
    {
        $this->registerUserTypes($config);

        return $this->builder->createModel($config);
    }

    /**
     * @param Config $config
     */
    protected function registerUserTypes(Config $config)
    {
        $userTypes = $config->get('db_types');
        if ($userTypes && is_array($userTypes)) {
            $connection = $config->get('connection');

            foreach ($userTypes as $type => $value) {
                $this->typeRegistry->registerType($type, $value, $connection);
            }
        }
    }
}
