<?php

namespace Krlove\EloquentModelGenerator\Processor;

use Krlove\EloquentModelGenerator\Config\Config;
use Krlove\EloquentModelGenerator\Model\EloquentModel;

interface ProcessorInterface
{
    public function process(EloquentModel $model, Config $config): void;
    public function getPriority(): int;
}
