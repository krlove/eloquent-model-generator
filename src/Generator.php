<?php

namespace Krlove\EloquentModelGenerator;

use IteratorAggregate;
use Krlove\EloquentModelGenerator\Config\Config;
use Krlove\EloquentModelGenerator\Model\EloquentModel;
use Krlove\EloquentModelGenerator\Processor\ProcessorInterface;

class Generator
{
    /**
     * @var ProcessorInterface[]
     */
    protected array $processors;

    /**
     * @param ProcessorInterface[]|IteratorAggregate $processors
     */
    public function __construct(iterable $processors)
    {
        if ($processors instanceof IteratorAggregate) {
            $this->processors = iterator_to_array($processors);
        } else {
            $this->processors = $processors;
        }
    }

    public function generateModel(Config $config): EloquentModel
    {
        $model = new EloquentModel();

        $this->sortProcessorsByPriority();

        foreach ($this->processors as $processor) {
            $processor->process($model, $config);
        }

        return $model;
    }

    protected function sortProcessorsByPriority(): void
    {
        usort($this->processors, function (ProcessorInterface $one, ProcessorInterface $two) {
            if ($one->getPriority() == $two->getPriority()) {
                return 0;
            }

            return $one->getPriority() < $two->getPriority() ? 1 : -1;
        });
    }
}
