<?php

namespace Krlove\EloquentModelGenerator;

use Krlove\EloquentModelGenerator\Exception\GeneratorException;
use Krlove\CodeGenerator\Model\ClassModel;

/**
 * Class Generator
 * @package Krlove\Generator
 */
class Generator
{
    /**
     * @var EloquentModelBuilder
     */
    protected $builder;

    /**
     * Generator constructor.
     * @param EloquentModelBuilder $builder
     */
    public function __construct(EloquentModelBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @param Config $config
     * @return ClassModel
     * @throws GeneratorException
     */
    public function generateModel(Config $config)
    {
        $model   = $this->builder->createModel($config);
        $content = $model->render();

        $outputPath = $this->resolveOutputPath($config);
        file_put_contents($outputPath, $content);

        return $model;
    }

    /**
     * @param Config $config
     * @return string
     */
    protected function resolveOutputPath(Config $config)
    {
        return $config->get('output_path', app_path()) . '/' . $config->get('class_name') . '.php';
    }
}
