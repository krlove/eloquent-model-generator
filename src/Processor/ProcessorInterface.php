<?php

namespace Krlove\EloquentModelGenerator\Processor;

use Krlove\EloquentModelGenerator\Config;
use Krlove\EloquentModelGenerator\Model\EloquentModel;

/**
 * Interface ProcessorInterface
 * @package Krlove\EloquentModelGenerator\Processor
 */
interface ProcessorInterface
{
    /**
     * @param EloquentModel $model
     * @param Config $config
     */
    public function process(EloquentModel $model, Config $config);

    /**
     * @return int
     */
    public function getPriority();
}
