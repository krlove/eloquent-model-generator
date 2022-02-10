<?php

namespace Krlove\EloquentModelGenerator;

use Krlove\EloquentModelGenerator\Model\EloquentModel;

class Generator
{
    protected EloquentModelBuilder $builder;
    protected TypeRegistry $typeRegistry;

    public function __construct(EloquentModelBuilder $builder, TypeRegistry $typeRegistry)
    {
        $this->builder = $builder;
        $this->typeRegistry = $typeRegistry;
    }

    public function generateModel(Config $config): EloquentModel
    {
        $this->registerUserTypes($config);

        return $this->builder->createModel($config);
    }

    protected function registerUserTypes(Config $config): void
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
