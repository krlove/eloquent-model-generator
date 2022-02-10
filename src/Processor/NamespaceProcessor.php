<?php

namespace Krlove\EloquentModelGenerator\Processor;

use Krlove\CodeGenerator\Model\NamespaceModel;
use Krlove\EloquentModelGenerator\Config;
use Krlove\EloquentModelGenerator\Model\EloquentModel;

class NamespaceProcessor implements ProcessorInterface
{
    public function process(EloquentModel $model, Config $config): void
    {
        $model->setNamespace(new NamespaceModel($config->get('namespace')));
    }

    public function getPriority(): int
    {
        return 6;
    }
}
