<?php

namespace Krlove\Generator;

use Krlove\Generator\Exception\GeneratorException;
use Krlove\Generator\Model\EloquentModel;

/**
 * Class Generator
 * @package Krlove\Generator
 */
class Generator
{
    /**
     * @var Renderer
     */
    protected $renderer;

    /**
     * @var EloquentModelBuilder
     */
    protected $builder;

    /**
     * Generator constructor.
     * @param EloquentModelBuilder $builder
     * @param Renderer             $renderer
     */
    public function __construct(EloquentModelBuilder $builder, Renderer $renderer)
    {
        $this->builder  = $builder;
        $this->renderer = $renderer;
    }

    /**
     * @param Config $config
     * @return EloquentModel
     * @throws GeneratorException
     */
    public function generateModel(Config $config)
    {
        $this->validateConfig($config);
        $model = $this->builder->createModel($config);
        $content = $model->render()->__toString();

        $outputPath = $this->resolveOutputPath($config->get('output_path'), $model);
        file_put_contents($outputPath, $content);

        return $model;
    }

    /**
     * @param Config $config
     * @throws GeneratorException
     */
    protected function validateConfig(Config $config)
    {
        if (!$config->has('table_name')) {
            throw new GeneratorException('Table name must be specified');
        }
        if (!$config->has('output_path')) {
            throw new GeneratorException('Output path must be specified');
        }
    }

    /**
     * @param string $dir
     * @param EloquentModel $model
     * @return string
     */
    protected function resolveOutputPath($dir, EloquentModel $model)
    {
        return $dir . '/' . $model->getName()->getName() . '.php';
    }
}
