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
        $this->builder  = $builder;
    }

    /**
     * @param Config $config
     * @return ClassModel
     * @throws GeneratorException
     */
    public function generateModel(Config $config)
    {
        $this->validateConfig($config);
        $model = $this->builder->createModel($config);
        $content = $model->render();

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
     * @param ClassModel $model
     * @return string
     */
    protected function resolveOutputPath($dir, ClassModel $model)
    {
        return $dir . '/' . $model->getName()->getName() . '.php';
    }
}
