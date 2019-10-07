<?php

namespace Krlove\EloquentModelGenerator;

use Krlove\EloquentModelGenerator\Exception\GeneratorException;
use Krlove\EloquentModelGenerator\Model\EloquentModel;
use Krlove\EloquentModelGenerator\Processor\ProcessorInterface;
use Illuminate\Container\RewindableGenerator;

/**
 * Class EloquentModelBuilder
 * @package Krlove\EloquentModelGenerator
 */
class EloquentModelBuilder
{
    /**
     * @var ProcessorInterface[]
     */
    protected $processors;

    /**
     * EloquentModelBuilder constructor.
     * @param ProcessorInterface[]| RewindableGenerator $processors
     */
    public function __construct($processors)
    {
        // Rewritten to cope both with an array and an iterator. Laravel 6.0 compatibility
        foreach ($processors as $processor) {
            if (!$processor instanceof  ProcessorInterface) {
                throw new \ErrorException("Expecting ProcessorInterface subclass, passed ". get_class($processor));
            }
            $this->processors[]=$processor;
        }
    }

    /**
     * @param Config $config
     * @return EloquentModel
     * @throws GeneratorException
     */
    public function createModel(Config $config)
    {
        $model = new EloquentModel();

        $this->prepareProcessors();

        foreach ($this->processors as $processor) {
            $processor->process($model, $config);
        }

        return $model;
    }

    /**
     * Sort processors by priority
     */
    protected function prepareProcessors()
    {
        usort($this->processors, function (ProcessorInterface $one, ProcessorInterface $two) {
            if ($one->getPriority() == $two->getPriority()) {
                return 0;
            }

            return $one->getPriority() < $two->getPriority() ? 1 : -1;
        });
    }
}
