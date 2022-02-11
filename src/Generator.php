<?php

namespace Krlove\EloquentModelGenerator;

use Krlove\EloquentModelGenerator\Model\EloquentModel;

class Generator
{
    public function __construct(private EloquentModelBuilder $builder, private TypeRegistry $typeRegistry) {}

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
